#!/usr/bin/env python3
"""
llm/bedrock_runner.py — Bedrock live inference shim for copilot-sessions-hq agents.

Drop-in replacement for the live backend side of:
  agent-exec-next.sh -> run_bedrock()

Unlike scripts/bedrock-assist.sh, this runner does not depend on site-local Drupal
services. It uses the HQ session cache model so backend choice does not change
seat/runtime semantics.
"""

from __future__ import annotations

import argparse
import json
import os
import sys
from pathlib import Path
from typing import List

REPO_ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(REPO_ROOT))

from llm.runner import load_session, save_session  # noqa: E402


DEFAULT_MODEL_ID = "us.anthropic.claude-sonnet-4-6"
DEFAULT_MAX_TOKENS = 2048
DEFAULT_REGION = os.environ.get("AWS_REGION") or os.environ.get("AWS_DEFAULT_REGION") or "us-east-1"


def _build_messages(history: List[dict], prompt: str) -> List[dict]:
    messages: List[dict] = []
    for msg in history:
        role = str(msg.get("role") or "").strip().lower()
        content = str(msg.get("content") or "")
        if role not in {"user", "assistant"} or not content.strip():
            continue
        messages.append({"role": role, "content": [{"type": "text", "text": content}]})
    messages.append({"role": "user", "content": [{"type": "text", "text": prompt}]})
    return messages


def _extract_text(response: dict) -> str:
    output = (((response or {}).get("output") or {}).get("message") or {})
    content = output.get("content") or []
    parts: List[str] = []
    for block in content:
        text = block.get("text")
        if text:
            parts.append(str(text))
    return "\n".join(parts).strip()


def _extract_invoke_model_text(body: dict) -> str:
    content = body.get("content") or []
    parts: List[str] = []
    for block in content:
        text = block.get("text")
        if text:
            parts.append(str(text))
    return "\n".join(parts).strip()


def run_bedrock(
    session_id: str,
    prompt: str,
    *,
    model_id: str,
    max_tokens: int,
    no_history: bool,
    region_name: str,
) -> str:
    try:
        import boto3
        from botocore.exceptions import BotoCoreError, ClientError
    except ImportError as exc:  # pragma: no cover
        raise RuntimeError("boto3/botocore are required for Bedrock runner") from exc

    history = [] if no_history else load_session(session_id)
    messages = _build_messages(history, prompt)

    client = boto3.client("bedrock-runtime", region_name=region_name)
    try:
        if hasattr(client, "converse"):
            response = client.converse(
                modelId=model_id,
                messages=messages,
                inferenceConfig={
                    "maxTokens": max_tokens,
                    "temperature": 0,
                },
            )
            text = _extract_text(response)
        else:
            payload = {
                "anthropic_version": "bedrock-2023-05-31",
                "max_tokens": max_tokens,
                "temperature": 0,
                "messages": messages,
            }
            response = client.invoke_model(
                modelId=model_id,
                body=json.dumps(payload),
                contentType="application/json",
                accept="application/json",
            )
            raw_body = response.get("body")
            if hasattr(raw_body, "read"):
                raw_body = raw_body.read()
            if isinstance(raw_body, bytes):
                raw_body = raw_body.decode("utf-8")
            text = _extract_invoke_model_text(json.loads(raw_body or "{}"))
    except (BotoCoreError, ClientError, ValueError) as exc:
        raise RuntimeError(f"Bedrock inference failed: {exc}") from exc

    if not text:
        raise RuntimeError("Bedrock returned an empty response")

    if not no_history:
        history.append({"role": "user", "content": prompt})
        history.append({"role": "assistant", "content": text})
        save_session(session_id, history)
    return text


def main() -> None:
    parser = argparse.ArgumentParser(description="Bedrock live inference shim for HQ agents.")
    parser.add_argument("--session", default="default", help="Session ID for conversation history.")
    parser.add_argument("--prompt", default="", help="Prompt text. Reads from stdin if omitted.")
    parser.add_argument("--model-id", default=DEFAULT_MODEL_ID, help="Bedrock model ID.")
    parser.add_argument("--max-tokens", type=int, default=DEFAULT_MAX_TOKENS, help="Maximum output tokens.")
    parser.add_argument("--region", default=DEFAULT_REGION, help="AWS region for Bedrock runtime.")
    parser.add_argument("--no-history", action="store_true", help="Ignore and do not write session history.")
    args = parser.parse_args()

    prompt = args.prompt or sys.stdin.read()
    if not prompt.strip():
        print("ERROR: prompt is required", file=sys.stderr)
        raise SystemExit(1)

    try:
        text = run_bedrock(
            args.session,
            prompt,
            model_id=args.model_id,
            max_tokens=max(1, int(args.max_tokens)),
            no_history=bool(args.no_history),
            region_name=str(args.region),
        )
    except RuntimeError as exc:
        print(f"ERROR: {exc}", file=sys.stderr)
        raise SystemExit(2) from exc

    print(text)


if __name__ == "__main__":
    main()
