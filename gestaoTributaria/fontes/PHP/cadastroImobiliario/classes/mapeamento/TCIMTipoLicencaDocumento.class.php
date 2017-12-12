<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
     * Classe de mapeamento para a tabela IMOBILIARIO.tipo_licenca_documento
     * Data de Criação: 25/03/2008

     * @author Analista: Fabio Bertoldi
     * @author Desenvolvedor: Fernando Piccini Cercato

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMTipoLicencaDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMTipoLicencaDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMTipoLicencaDocumento()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.tipo_licenca_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_tipo,cod_tipo_documento,cod_documento');

        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_tipo_documento', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_documento', 'integer', true, '', true, true );
    }

    public function retornaListadeDocumentosLicenca(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRetornaListadeDocumentosLicenca().$stFiltro;
        $this->setDebug( $stSql );
        //$this->debug();exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRetornaListadeDocumentosLicenca()
    {
        $stSql = "
            SELECT
                tipo_licenca_documento.cod_documento,
                tipo_licenca_documento.cod_tipo_documento,
                modelo_documento.nome_documento

            FROM
                imobiliario.tipo_licenca_documento

            INNER JOIN
                administracao.modelo_documento
            ON
                modelo_documento.cod_documento = tipo_licenca_documento.cod_documento
                AND modelo_documento.cod_tipo_documento = tipo_licenca_documento.cod_tipo_documento
        ";

        return $stSql;
    }

    public function retornaListadeDocumentosDisponiveis(&$rsRecordSet, $stFiltro = "", $inCodTipoLicenca = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRetornaListadeDocumentosDisponiveis($inCodTipoLicenca).$stFiltro;
        $this->setDebug( $stSql );
        //$this->debug();exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRetornaListadeDocumentosDisponiveis($inCodTipoLicenca)
    {
        $stSql = "
            SELECT
                modelo_documento.cod_documento,
                modelo_documento.nome_documento,
                modelo_documento.nome_arquivo_agt,
                modelo_documento.cod_tipo_documento

            FROM
                administracao.modelo_arquivos_documento

            INNER JOIN
                administracao.modelo_documento
            ON
                modelo_documento.cod_documento = modelo_arquivos_documento.cod_documento
                AND modelo_documento.cod_tipo_documento = modelo_arquivos_documento.cod_tipo_documento

            LEFT JOIN
                imobiliario.tipo_licenca_documento
            ON
                tipo_licenca_documento.cod_documento = modelo_documento.cod_documento
                AND tipo_licenca_documento.cod_tipo_documento = modelo_documento.cod_tipo_documento ";

            if ($inCodTipoLicenca) {
                $stSql .= " AND tipo_licenca_documento.cod_tipo = ".$inCodTipoLicenca." ";
            }

        return $stSql;
    }

}
