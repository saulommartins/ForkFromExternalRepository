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
    * Classe de mapeamento da tabela licitacao.modalidade_documentos
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 17482 $
    $Name$
    $Author: larocca $
    $Date: 2006-11-08 08:51:42 -0200 (Qua, 08 Nov 2006) $

    * Casos de uso: uc-03.05.12
*/
/*
$Log$
Revision 1.3  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.2  2006/11/03 18:28:39  fernando
método para recuperar os documentos da licitação

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.modalidade_documentos
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoModalidadeDocumentos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoModalidadeDocumentos()
{
    parent::Persistente();
    $this->setTabela("licitacao.modalidade_documentos");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_documento,cod_modalidade');

    $this->AddCampo('cod_documento' ,'integer',false ,'',true,'TLicitacaoDocumento');
    $this->AddCampo('cod_modalidade','integer',false ,'',true,'TComprasModalidade');

}

function recuperaDocumentos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDocumentos()
{
$stSql ="SELECT                                        \n";
$stSql .="    d.cod_documento                           \n";
$stSql .="    ,d.nom_documento                          \n";
$stSql .="FROM                                          \n";
$stSql .="     licitacao.modalidade_documentos as md    \n";
$stSql .="    ,licitacao.documento as d                 \n";
$stSql .="WHERE                                         \n";
$stSql .="    md.cod_documento = d.cod_documento        \n";
if ($this->getDado('cod_modalidade'))
    $stSql .="    AND md.cod_modalidade = ".$this->getDado('cod_modalidade') ." \n";

    return $stSql;
}

}
