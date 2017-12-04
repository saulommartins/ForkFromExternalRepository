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
    * Classe de mapeamento da tabela licitacao.membro_adicional
    * Data de CriaÃ§Ã£o: 31/10/2006

    * @author Analista: Gelson W. GonÃ§alves
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 17767 $
    $Name$
    $Author: fernando $
    $Date: 2006-11-16 16:21:01 -0200 (Qui, 16 Nov 2006) $

    * Casos de uso: uc-03.05.17
*/
/*
$Log$
Revision 1.3  2006/11/16 18:20:31  fernando
alterações para o alterar processo licitatorio

Revision 1.2  2006/11/14 18:19:00  fernando
função para trazer os membros adicionais da licitacao

Revision 1.1  2006/10/31 19:15:11  fernando
classe de mapeamento da tabela licitacao.membro_adicional

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexÃ£o com a tabela  licitacao.membro_adicional
  * Data de CriaÃ§Ã£o: 15/09/2006

  * @author Analista: Gelson W. GonÃ§alves
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoMembroAdicional extends Persistente
{
/**
    * MÃ©todo Construtor
    * @access Private
*/
function TLicitacaoMembroAdicional()
{
    parent::Persistente();
    $this->setTabela("licitacao.membro_adicional");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_licitacao,cod_modalidade,cod_entidade,exercicio,numcgm');

    $this->AddCampo('cod_entidade'      ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
    $this->AddCampo('cod_modalidade'    ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
    $this->AddCampo('cod_licitacao'     ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
    $this->AddCampo('exercicio'         ,'char'   ,false ,'4'  ,true,'TLicitacaoLicitacao');
    $this->AddCampo('numcgm'            ,'integer',false ,''   ,true,'TCGM');
    $this->AddCampo('cargo'             ,'char'   ,false ,'50' ,false);
    $this->AddCampo('natureza_cargo'    ,'integer',false ,''   ,false);

}
function recuperaMembroAdicional(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMembroAdicional().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}
function montaRecuperaMembroAdicional()
{
    $stSql ="SELECT                                  \n";
    $stSql .="     MA.cod_entidade                   \n";
    $stSql .="    ,MA.cod_licitacao                  \n";
    $stSql .="    ,cgm.nom_cgm                       \n";
    $stSql .="    ,cgm.numcgm                        \n";
    $stSql .="    ,MA.exercicio                      \n";
    $stSql .="    ,MA.cod_modalidade                 \n";
    $stSql .="    ,MA.cargo                          \n";
    $stSql .="    ,MA.natureza_cargo                 \n";
    $stSql .="FROM                                   \n";
    $stSql .="     licitacao.membro_adicional as MA  \n";
    $stSql .="    ,sw_cgm as cgm                     \n";
    $stSql .="WHERE                                  \n";
    $stSql .="    MA.numcgm = cgm.numcgm             \n";

    if ($this->getDado('cod_entidade'))
        $stSql.=" AND MA.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    if ($this->getDado('cod_modalidade'))
        $stSql.=" AND MA.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";
    if ($this->getDado('cod_licitacao'))
        $stSql.=" AND MA.cod_licitacao = ".$this->getDado('cod_licitacao')." \n";
    if ($this->getDado('exercicio'))
        $stSql.=" AND MA.exercicio = '".$this->getDado('exercicio')."' \n";
    return $stSql;

}
}
