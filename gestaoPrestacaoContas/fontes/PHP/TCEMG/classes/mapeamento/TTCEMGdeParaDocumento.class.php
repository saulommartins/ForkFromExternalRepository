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
    * Classe de mapeamento da tabela tcemg.de_para_documento
    * Data de Criação: 19/05/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Evandro Noguez Melos

    * @package URBEM
    * @subpackage Mapeamento
    $Id: TTCEMGdeParaDocumento.class.php 59719 2014-09-08 15:00:53Z franver $
*/
/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGdeParaDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEMGdeParaDocumento()
{
    parent::Persistente();
    $this->setTabela("tcemg.de_para_documento");

    $this->setCampoCod('cod_doc_tce');
    $this->setComplementoChave('cod_doc_urbem');

    $this->AddCampo('cod_doc_tce'       ,'integer'  ,true   ,''     ,true,true);
    $this->AddCampo('cod_doc_urbem'     ,'integer'  ,true   ,''     ,true,true);

}

function recuperaDocumentosDePara(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentosDePara().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDocumentosDePara()
{
    $stSql = "  select *                          ";
    $stSql.= "  from tcemg.de_para_documento      ";

    return $stSql;
}

public function __destruct(){}


}//fim classe
?>