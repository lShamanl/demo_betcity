#!/bin/bash

FROM nginx:1.25-alpine

RUN apk add --no-cache curl

COPY ./common/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app
