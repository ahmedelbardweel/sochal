FROM golang:1.21-alpine

WORKDIR /app

COPY go.mod ./
RUN go mod download

COPY . .
RUN go build -o sfu main.go

EXPOSE 8888
EXPOSE 50000-50100/udp

CMD ["./sfu"]
