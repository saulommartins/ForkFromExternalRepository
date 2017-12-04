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
    * Classe de mapeamento da tabela empenho.prestacao_contas
    * Data de Criação: 09/05/2007

    * @author Analista:
    * @author Desenvolvedor:

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: luciano $
    $Date: 2007-05-15 11:48:32 -0300 (Ter, 15 Mai 2007) $

    * Casos de uso: uc-02.03.31
*/
/*
$Log$
Revision 1.2  2007/05/15 14:35:42  luciano
#9104#

Revision 1.1  2007/05/10 14:02:04  luciano
Adicionando ao repositorio

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadePrestacaoContas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadePrestacaoContas()
{
    parent::Persistente();
    $this->setTabela("contabilidade.prestacao_contas");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,cod_entidade,exercicio,tipo,sequencia');

    $this->AddCampo('cod_lote'                      ,'integer',true  ,''   ,true,'TContabilidadeLancamentoEmpenho');
    $this->AddCampo('cod_entidade'                  ,'integer',true  ,''   ,true,'TContabilidadeLancamentoEmpenho');
    $this->AddCampo('exercicio'                     ,'char'   ,true  ,'4'  ,true,'TContabilidadeLancamentoEmpenho');
    $this->AddCampo('tipo'                          ,'char'   ,true  ,'1'  ,true,'TContabilidadeLancamentoEmpenho');
    $this->AddCampo('sequencia'                     ,'integer',true  ,''   ,true,'TContabilidadeLancamentoEmpenho');
    $this->AddCampo('exercicio_prestacao_contas'    ,'char'   ,true  ,'4'  ,true,'TEmpenhoItemPrestacaoContas');
    $this->AddCampo('cod_empenho'                   ,'integer',true  ,''   ,true,'TEmpenhoItemPrestacaoContas');
    $this->AddCampo('num_item'                      ,'integer',true  ,''   ,true,'TEmpenhoItemPrestacaoContas');

}

function insereLote(&$inCodLote, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaInsereLote();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $inCodLote = $rsRecordSet->getCampo ( 'cod_lote' );

    return $obErro;
}

function montaInsereLote()
{
    $stSql  = " SELECT  \n";
    $stSql .= "      contabilidade.fn_insere_lote( ";
    $stSql .= " '".$this->getDado('exercicio')."' ";
    $stSql .= " ,".$this->getDado('cod_entidade');
    $stSql .= " ,'".$this->getDado('tipo')."' ";
    $stSql .= " ,'".$this->getDado('nom_lote')."' ";
    $stSql .= " ,'".$this->getDado('dt_lote')."' ";
    $stSql .= " ) as cod_lote \n";

    return $stSql ;
}

}
?>
