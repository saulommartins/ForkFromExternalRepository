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
    * Classe de mapeamento da tabela licitacao.licitacao
    * Data de CriaÃ§Ã£o: 15/09/2006

    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.15

    $Id: TLicitacaoComissaoLicitacao.class.php 66191 2016-07-28 14:03:35Z carlos.silva $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.licitacao
  * Data de CriaÃ§Ã£o: 15/09/2006

  * @author Analista: Gelson W. GonÃ§alves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoComissaoLicitacao extends Persistente
{
/**
    * MÃ©todo Construtor
    * @access Private
*/
function TLicitacaoComissaoLicitacao()
{
    parent::Persistente();
    $this->setTabela("licitacao.comissao_licitacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_entidade, cod_modalidade, cod_licitacao, cod_comissao');

    $this->AddCampo('cod_comissao'   , 'sequence' , true  , ''  ,true  , false);
    $this->AddCampo('cod_licitacao'  , 'sequence' , true  , ''  ,true  , 'TLicitacaoLicitacao');
    $this->AddCampo('cod_entidade'   , 'integer'  , false , ''  ,true  , 'TLicitacaoLicitacao');
    $this->AddCampo('exercicio'      , 'char'     , false , '4' ,true  , 'TLicitacaoLicitacao');
    $this->AddCampo('cod_modalidade' , 'integer'  , false , ''  ,false , 'TLicitacaoLicitacao');

}

function recuperaComissaoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaComissaoLicitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaComissaoLicitacao()
{
    $stSql  ="      SELECT                                                  \n";
    $stSql .="                cl.cod_licitacao                              \n";
    $stSql .="              , cl.cod_comissao                               \n";
    $stSql .="              , CASE when c.cod_tipo_comissao = 4 THEN        \n";
    $stSql .="                  'apoio'                                     \n";
    $stSql .="                ELSE 'comissao'                               \n";
    $stSql .="                 END AS equipe                                \n";
    $stSql .= "             , TO_CHAR(norma.dt_publicacao,'dd/mm/yyyy') as dt_publicacao        \n";
    $stSql .= "             , TO_CHAR(norma_data_termino.dt_termino,'dd/mm/yyyy') as dt_termino \n";
    $stSql .= "             , tipo_comissao.descricao as finalidade         \n";
    $stSql .= "             , tipo_comissao.cod_tipo_comissao               \n";
    $stSql .= "             , norma.cod_norma                               \n";
    $stSql .="                                                              \n";
    $stSql .="        FROM                                                  \n";
    $stSql .="                licitacao.comissao_licitacao as cl            \n";
    $stSql .="              , licitacao.comissao as c                       \n";
    $stSql .="                                                              \n";
    $stSql .= " INNER JOIN  licitacao.tipo_comissao                               \n " ;
    $stSql .= "         ON  tipo_comissao.cod_tipo_comissao = c.cod_tipo_comissao \n " ;
    $stSql .="                                                              \n";
    $stSql .= " INNER JOIN  normas.norma                                    \n";
    $stSql .= "         ON  norma.cod_norma = c.cod_norma                   \n";
    $stSql .="                                                              \n";
    $stSql .= "  LEFT JOIN  normas.norma_data_termino                       \n";
    $stSql .= "         ON  norma_data_termino.cod_norma = norma.cod_norma  \n";
    $stSql .="                                                              \n";
    $stSql .= "  LEFT JOIN  normas.tipo_norma ntn                           \n";
    $stSql .= "         ON  norma.cod_tipo_norma = ntn.cod_tipo_norma       \n";
    $stSql .="                                                              \n";
    $stSql .="       WHERE  cl.cod_comissao = c.cod_comissao                \n";

    if ($this->getDado('cod_licitacao'))
        $stSql .=" AND cl.cod_licitacao = ".$this->getDado('cod_licitacao')." \n";

    if ($this->getDado('cod_modalidade'))
        $stSql .=" AND cl.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";

    if ($this->getDado('cod_entidade'))
        $stSql .=" AND cl.cod_entidade = ".$this->getDado('cod_entidade')." \n";

    if ($this->getDado('exercicio'))
        $stSql .=" AND cl.exercicio = '".$this->getDado('exercicio')."' \n";

    return $stSql;
}

function recuperaMembro(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMembro().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}
function montaRecuperaMembro()
{
$stSql  ="SELECT \n";
$stSql .="    cm.cod_tipo_membro       \n";
$stSql .="   ,sw_cgm.nom_cgm                  \n";
$stSql .="FROM \n";
$stSql .="     licitacao.comissao_licitacao as cl \n";
$stSql .="    ,licitacao.comissao as c \n";
$stSql .="    ,sw_cgm                           \n";
$stSql .="    ,(select numcgm, cod_comissao, cod_tipo_membro ";
$stSql .="       from licitacao.comissao_membros   ";
$stSql .="      where cod_tipo_membro = 2     ";
$stSql .="         or cod_tipo_membro = 3) as cm     ";
$stSql .="WHERE \n";
$stSql .="    cl.cod_comissao = c.cod_comissao \n";
$stSql .=" and cm.numcgm = sw_cgm.numcgm       \n";
$stSql .=" and c.cod_comissao  = cm.cod_comissao \n";
$stSql .=" and c.cod_tipo_comissao <> 4       \n";
$stSql .=" AND cl.cod_licitacao = ".$this->getDado('cod_licitacao')." \n";
$stSql .=" AND cl.exercicio = '".$this->getDado('exercicio')."' \n";
return $stSql;

}

    public function recuperaMembroResponsavel(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMembroResponsavel().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaMembroResponsavel()
    {
        $stSql = "SELECT *
                    FROM licitacao.comissao_membros
              INNER JOIN licitacao.comissao
                      ON comissao_membros.cod_comissao = comissao.cod_comissao
              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = comissao_membros.numcgm
              INNER JOIN licitacao.tipo_membro
                      ON tipo_membro.cod_tipo_membro = comissao_membros.cod_tipo_membro
                   WHERE comissao_membros.cod_tipo_membro IN (2,3)
                     AND comissao_membros.cod_comissao IN (SELECT max(cod_comissao)
                                                             FROM licitacao.comissao
                                                            WHERE ativo IS TRUE);";

        return $stSql;
    }

    public function recuperaComissaoLicitacaoEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaComissaoLicitacaoEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function montaRecuperaComissaoLicitacaoEsfinge()
    {
        $stSql = "
                    select to_char(norma.dt_assinatura,'dd/mm/yyyy') as dt_assinatura
                    , comissao.cod_comissao
                    , to_char(norma_data_termino.dt_termino,'dd/mm/yyyy') as dt_termino
                    , tipo_comissao.descricao
                    from licitacao.licitacao

                    join licitacao.comissao_licitacao
                    on comissao_licitacao.cod_licitacao = licitacao.cod_licitacao
                    and comissao_licitacao.cod_modalidade = licitacao.cod_modalidade
                    and comissao_licitacao.cod_entidade = licitacao.cod_entidade
                    and comissao_licitacao.exercicio = licitacao.exercicio

                    join licitacao.comissao
                    on comissao.cod_comissao = comissao_licitacao.cod_comissao

                    join normas.norma
                    on norma.cod_norma = comissao.cod_norma

                    join normas.norma_data_termino
                    on norma_data_termino.cod_norma = norma.cod_norma

                    join licitacao.tipo_comissao
                    on tipo_comissao.cod_tipo_comissao = comissao.cod_tipo_comissao

                    where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
                    and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
                    and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
                    and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
        ";

        return $stSql;
    }

    public function recuperaValidaVigenciaComissao(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaValidaVigenciaComissao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function montaRecuperaValidaVigenciaComissao()
    {
        $stSql =" Select to_char(norma.dt_publicacao,'dd/mm/yyyy') as dt_publicacao,
                         to_char(norma_data_termino.dt_termino,'dd/mm/yyyy') as dt_termino,
                         norma.exercicio,
                         norma.cod_norma
                    from normas.norma
                    left join normas.norma_data_termino
                           on (norma_data_termino.cod_norma = norma.cod_norma)";

        return $stSql;
    }

}
