#!/bin/bash

FROM --platform=linux/amd64 nginx:1.25-alpine

COPY ./docker/common/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY ./public ./public
