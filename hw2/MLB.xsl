<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:template match="/mixedteams">
		<html>
			<head>
					<meta charset= "utf-8"/>
					<link rel="stylesheet" type="text/css" href="mlb.css"/>
					<title>Baseball</title>
			</head>
			<body>
				<xsl:for-each select="baseball">
					<xsl:variable name = "bc" select="BColor"/>
					<xsl:variable name = "c" select="Color"/>
					<table class = "basetable" border = "1" style="color: {$c}; background: {$bc};">
						<tr class = "table_top">
							<th colspan = "4"><xsl:value-of select="Team"/></th>
						</tr>
						<tr>
							<td>Image</td>
							<td>star</td>
							<td>Coach</td>
							<td>League</td>
						</tr>
						<tr class = "table_bottom">
							<td>
								<img>
									<xsl:attribute name="src">
										<xsl:value-of select="Image"/>
									</xsl:attribute>
								</img>
							</td>
							<td>
								<table class = "innertable" border = "3" style="color: {$c}; background: {$bc};">
									<xsl:for-each select="star">
										<td><xsl:value-of select="name"/></td>
										<td><xsl:value-of select="birth"/></td>
									</xsl:for-each>
								</table>
							</td>
							<td><xsl:value-of select="Coach"/></td>
							<td><xsl:value-of select="League"/></td>
						</tr>
						<tr>
							<td colspan = "4">
								<iframe>
									<xsl:attribute name="src">
										<xsl:value-of select="Video"/>
									</xsl:attribute>
								</iframe>
							</td>
						</tr>
					</table>
				</xsl:for-each>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
