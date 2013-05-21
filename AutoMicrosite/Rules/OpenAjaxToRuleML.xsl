<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://ruleml.org/spec"
	xmlns:oa="http://openajax.org/metadata"
	xmlns:oax="http://deepweb.ut.ee/automicrosite/OpenAjaxMetadataExtension"

	xmlns:oaxx="http://automicrosite.maesalu.com/OpenAjaxMetadataExtension"

	version="1.0">

	<xsl:param name="widget" select="999" />

	<xsl:template match="/">
		<RuleML>
			<Assert>

				<xsl:for-each select="//oa:category">
					<Atom>
						<Rel>http://openajax.org/metadata#category</Rel>
						<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
						<xsl:if test="@oax:iri">
							<slot><Ind>category</Ind><Ind><xsl:value-of select="@oax:iri" /></Ind></slot>
						</xsl:if>
						<xsl:if test="@oaxx:iri">
							<slot><Ind>category</Ind><Ind><xsl:value-of select="@oaxx:iri" /></Ind></slot>
						</xsl:if>
					</Atom>
				</xsl:for-each>

				<Atom>
					<Rel>http://deepweb.ut.ee/automicrosite/widget</Rel>
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

				<!-- Dimensions -->
				<xsl:if test="//@oax:min-width">
					<Atom>
						<Rel>http://deepweb.ut.ee/automicrosite/OpenAjaxMetadataExtension#min-width</Rel>
						<Ind><xsl:value-of select="$widget" /></Ind>
						<Ind type="Integer"><xsl:value-of select="//@oax:min-width" /></Ind>
					</Atom>
				</xsl:if>
				<xsl:if test="//@oax:max-width">
					<Atom>
						<Rel>http://deepweb.ut.ee/automicrosite/OpenAjaxMetadataExtension#max-width</Rel>
						<Ind><xsl:value-of select="$widget" /></Ind>
						<Ind type="Integer"><xsl:value-of select="//@oax:max-width" /></Ind>
					</Atom>
				</xsl:if>
				<xsl:if test="//@oax:min-height">
					<Atom>
						<Rel>http://deepweb.ut.ee/automicrosite/OpenAjaxMetadataExtension#min-height</Rel>
						<Ind><xsl:value-of select="$widget" /></Ind>
						<Ind type="Integer"><xsl:value-of select="//@oax:min-height" /></Ind>
					</Atom>
				</xsl:if>
				<xsl:if test="//@oax:max-height">
					<Atom>
						<Rel>http://deepweb.ut.ee/automicrosite/OpenAjaxMetadataExtension#max-height</Rel>
						<Ind><xsl:value-of select="$widget" /></Ind>
						<Ind type="Integer"><xsl:value-of select="//@oax:max-height" /></Ind>
					</Atom>
				</xsl:if>

			</Assert>
		</RuleML>

	</xsl:template>

</xsl:stylesheet>