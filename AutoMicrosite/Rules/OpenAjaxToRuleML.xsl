<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://ruleml.org/spec"
	xmlns:oa="http://openajax.org/metadata"
	xmlns:oax="http://automicrosite.maesalu.com/OpenAjaxMetadataExtension"
	version="1.0">

	<xsl:param name="widget" select="999" />

	<xsl:template match="/">
		<RuleML>
			<Assert>

				<xsl:for-each select="//oa:category">
					<Atom>
						<Rel>http://openajax.org/metadata#category</Rel>
						<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
						<slot><Ind>category</Ind><Ind><xsl:value-of select="@oax:iri" /></Ind></slot>
					</Atom>
				</xsl:for-each>

				<Atom>
					<Rel>http://automicrosite.maesalu.com/#widgetExists</Rel>
					<Ind><xsl:value-of select="$widget" /></Ind>
				</Atom>

				<Atom>
					<Rel>http://openajax.org/metadata#id</Rel>
					<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
					<slot><Ind>id</Ind><Ind><xsl:value-of select="//@id" /></Ind></slot>
				</Atom>

				<Atom>
					<Rel>http://openajax.org/metadata#width</Rel>
					<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
					<slot><Ind>width</Ind><Ind type="Integer"><xsl:value-of select="//@width" /></Ind></slot>
				</Atom>

				<Atom>
					<Rel>http://openajax.org/metadata#height</Rel>
					<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
					<slot><Ind>height</Ind><Ind type="Integer"><xsl:value-of select="//@height" /></Ind></slot>
				</Atom>

			</Assert>
		</RuleML>

	</xsl:template>

</xsl:stylesheet>