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
    * Classe de mapeamento para FISCALIZACAO.INICIO_FISCALIZACAO_DOCUMENTOS
    * Data de Criacao: 05/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Mapeamento

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CLA_PERSISTENTE );

class TFISInicioFiscalizacaoDocumentos extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function __construct()
    {
            parent::Persistente();
            $this->setTabela( 'fiscalizacao.inicio_fiscalizacao_documentos' );

            $this->setCampoCod( 'cod_processo' );
            $this->setComplementoChave( 'cod_documento' );

            $this->AddCampo( 'cod_processo','integer',true,'',false,true );
            $this->AddCampo( 'cod_documento','integer',true,'',false,true );
    }

    public function recuperarInicioFiscalizacaoDocumentos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarInicioFiscalizacaoDocumentos($stCondicao);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarInicioFiscalizacaoDocumentos($condicao)
    {
        $stSql =" SELECT ifd.cod_processo 					                    \n";
        $stSql.="      , ifd.cod_documento 		  			                    \n";
        $stSql.="   FROM fiscalizacao.inicio_fiscalizacao_documentos AS ifd 	\n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaPendenciasProcessoDocumentoEntregue(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaPendenciasProcessoDocumentoEntregue($stCondicao);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaPendenciasProcessoDocumentoEntregue($condicao)
    {
        $stSql = "SELECT                                                        \n";
        $stSql.= "    count(ifd.cod_documento) as documentos_existentes,        \n";
        $stSql.= "    count(de.cod_documento) as documentos_entregue,           \n";
        $stSql.= "    inf.cod_processo                                          \n";
        $stSql.= "FROM                                                          \n";
        $stSql.= "    fiscalizacao.inicio_fiscalizacao inf                      \n";
        $stSql.= "LEFT JOIN                                                     \n";
        $stSql.= "    fiscalizacao.inicio_fiscalizacao_documentos ifd           \n";
        $stSql.= "    on inf.cod_processo = ifd.cod_processo                    \n";
        $stSql.= "LEFT OUTER JOIN                                               \n";
        $stSql.= "    fiscalizacao.documentos_entrega de                        \n";
        $stSql.= "    on inf.cod_processo = de.cod_processo                     \n";
        $stSql.= $condicao."                                                    \n";
        $stSql.= "GROUP BY                                                      \n";
        $stSql.= "    inf.cod_processo                                          \n";

        return $stSql;
    }

    public function recuperaPendenciasProcessoDocumentoPrazo(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaPendenciasProcessoDocumentoPrazo($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaPendenciasProcessoDocumentoPrazo($condicao)
    {
        $stSql = "SELECT                                                        \n";
        $stSql.= "    inf.cod_processo as processo,                             \n";
        $stSql.= "    ifd.cod_documento as documento,                           \n";
        $stSql.= "    (CASE WHEN pp.dt_prorrogacao IS NOT NULL                  \n";
        $stSql.= "        THEN                                                  \n";
        $stSql.= "	        pp.dt_prorrogacao                                   \n";
        $stSql.= "        ELSE                                                  \n";
        $stSql.= "	        prazo_entrega		                                \n";
        $stSql.= "    END) as prazo,                                            \n";
        $stSql.= "    CAST(de.timestamp AS date) as entregou                    \n";
        $stSql.= "FROM                                                          \n";
        $stSql.= "    fiscalizacao.inicio_fiscalizacao inf                      \n";
        $stSql.= "LEFT JOIN                                                     \n";
        $stSql.= "    fiscalizacao.prorrogacao_entrega pp                       \n";
        $stSql.= "    on inf.cod_processo = pp.cod_processo                     \n";
        $stSql.= "INNER JOIN                                                    \n";
        $stSql.= "    fiscalizacao.inicio_fiscalizacao_documentos ifd           \n";
        $stSql.= "    on ifd.cod_processo = inf.cod_processo                    \n";
        $stSql.= "LEFT OUTER JOIN                                               \n";
        $stSql.= "    fiscalizacao.documentos_entrega de                        \n";
        $stSql.= "    on ifd.cod_documento = de.cod_documento                   \n";
        $stSql.= "WHERE                                                         \n";
        $stSql.= "    inf.cod_documento IS NOT NULL                             \n";
        $stSql.= "AND                                                           \n";
        $stSql.= $condicao."                                                    \n";
        $stSql.= "ORDER BY                                                      \n";
        $stSql.= "    pp.timestamp                                              \n";

        return $stSql;
    }

}

?>
