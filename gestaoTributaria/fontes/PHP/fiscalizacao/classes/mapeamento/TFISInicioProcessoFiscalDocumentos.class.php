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

            $stSql = $this->montaRecuperarInicioFiscalizacaoDocumentos($stCondicao).$stOrdem;
            $this->setDebug( $stSql );
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperarInicioFiscalizacaoDocumentos($condicao)
    {
            $stSql = " SELECT ifd.cod_processo, ifd.cod_documento
        FROM  fiscalizacao.inicio_fiscalizacao_documentos AS ifd ". $condicao;

        return $stSql;
    }

}

?>
