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
    * Classe de mapeamento da tabela licitacao.comissao_membros
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 21723 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-04-10 15:36:22 -0300 (Ter, 10 Abr 2007) $

    * Casos de uso: uc-03.05.09
*/
/*
$Log$
Revision 1.10  2007/04/10 18:30:03  andre.almeida
Adicionado consultas para o e-Sfinge.

Revision 1.9  2007/01/24 12:20:30  hboaventura
Bug #8081#

Revision 1.8  2007/01/18 17:43:28  hboaventura
Bug #8081#

Revision 1.7  2006/11/17 11:17:07  hboaventura
Bug #7400# e #7464#

Revision 1.6  2006/09/25 18:03:30  bruce
colocado o timeStamp atual na data do recibo

Revision 1.5  2006/09/25 16:02:49  bruce
colocado o UC

Revision 1.4  2006/09/25 14:22:17  bruce
colocado o UC

Revision 1.3  2006/09/22 17:56:28  bruce
alterção de comissões

Revision 1.2  2006/09/21 09:42:02  bruce
setadas as chaves estrangeiras

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.comissao_membros
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoComissaoMembros extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TLicitacaoComissaoMembros()
    {
        parent::Persistente();
        $this->setTabela("licitacao.comissao_membros");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_comissao,numcgm,cod_norma');

        $this->AddCampo('cod_comissao'   ,'integer',false ,'',true,'TLicitacaoComissao');
        $this->AddCampo('cod_tipo_membro','integer',false ,'',false,'TLicitacaoTipoMembro');
        $this->AddCampo('cod_norma'      ,'integer' ,false ,'',false,'TNormasNorma');
        $this->AddCampo('numcgm'         ,'integer' ,false ,'',false,'TCGMCGM');
        $this->AddCampo('cargo'          ,'varchar' ,false ,'50',false,false);
        $this->AddCampo('natureza_cargo' ,'integer' ,false ,'',false,false);

    }

function recuperaMembrosPorComissao(&$rsRecordSet, $cod_comissao, $boTransacao = '')
{
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMembrosPorComissao( $cod_comissao );
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

}

function montaRecuperaMembrosPorComissao($cod_comissao)
{
    $stSql = '';

    $stSql .= "select sw_cgm.nom_cgm,                                                                                \n ";
    $stSql .= "       tipo_membro.descricao as tipo_membro,                                                          \n ";
    $stSql .= "       to_char(norma.dt_publicacao,'dd/mm/yyyy') AS dt_publicacao,                                    \n ";
    $stSql .= "       comissao_membros.numcgm,                                                                       \n ";
    $stSql .= "       comissao_membros.cod_tipo_membro,                                                              \n ";
    $stSql .= "       comissao_membros.cod_norma,                                                                    \n ";
    $stSql .= "       comissao_membros.cod_comissao,                                                                  \n ";
    $stSql .= "       comissao_membros.cargo,                                                                         \n ";
    $stSql .= "       comissao_membros.natureza_cargo                                                                \n ";
    $stSql .= "from licitacao.comissao_membros                                                                       \n ";
    $stSql .= "join sw_cgm                                                                                           \n ";
    $stSql .= "    on ( comissao_membros.numcgm = sw_cgm.numcgm )                                                    \n ";
    $stSql .= "join licitacao.tipo_membro                                                                            \n ";
    $stSql .= "    on ( comissao_membros.cod_tipo_membro = tipo_membro.cod_tipo_membro )                             \n ";
    $stSql .= "join normas.norma                                                                                     \n ";
    $stSql .= "    on (comissao_membros.cod_norma = norma.cod_norma)                                                 \n ";
    $stSql .= " where comissao_membros.cod_comissao = $cod_comissao                                                  \n ";
    $stSql .= " and not  comissao_membros.cod_comissao::varchar|| comissao_membros.numcgm::varchar || comissao_membros.cod_norma::varchar in    \n ";
    $stSql .= "              ( select cod_comissao::varchar ||   numcgm::varchar || cod_norma::varchar                                          \n ";
    $stSql .= "                from licitacao.membro_excluido                                                        \n ";
    $stSql .= "                where membro_excluido.cod_comissao = $cod_comissao)                                   \n ";
    return $stSql;

}

function recuperaComissaoLicitacaoMembrosPorComissao(&$rsRecordSet, $cod_comissao, $cod_modalidade, $cod_licitacao, $boTransacao = '')
{
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaComissaoLicitacaoMembrosPorComissao( $cod_comissao, $cod_modalidade, $cod_licitacao );
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

}

function montaRecuperaComissaoLicitacaoMembrosPorComissao($cod_comissao, $cod_modalidade, $cod_licitacao)
{
    $stSql  = "  SELECT                                                                             \n";
    $stSql .= "         sw_cgm.nom_cgm                                                              \n";
    $stSql .= "       , ltm.descricao  as tipo_membro                                               \n";
    $stSql .= "       , to_char(nn.dt_publicacao,'dd/mm/yyyy') AS dt_publicacao                     \n";
    $stSql .= "       , lcm.numcgm                                                                  \n";
    $stSql .= "       , ltm.cod_tipo_membro                                                         \n";
    $stSql .= "       , nn.cod_norma                                                                \n";
    $stSql .= "       , lc.cod_comissao                                                             \n";
    $stSql .= "    FROM                                                                             \n";
    $stSql .= "         licitacao.comissao_membros as lcm                                           \n";
    $stSql .= "         JOIN sw_cgm                                                                 \n";
    $stSql .= "           ON sw_cgm.numcgm = lcm.numcgm                                             \n";
    $stSql .= "         JOIN licitacao.comissao as lc                                               \n";
    $stSql .= "           ON lc.cod_comissao = lcm.cod_comissao                                     \n";
    $stSql .= "         JOIN licitacao.tipo_membro as ltm                                           \n";
    $stSql .= "           ON ltm.cod_tipo_membro = lcm.cod_tipo_membro                              \n";
    $stSql .= "         JOIN normas.norma as nn                                                     \n";
    $stSql .= "           ON nn.cod_norma = lcm.cod_norma                                           \n";
    $stSql .= "         JOIN licitacao.comissao_licitacao_membros as lclm                           \n";
    $stSql .= "           ON lclm.cod_comissao = lcm.cod_comissao                                   \n";
    $stSql .= "          AND lclm.numcgm       = lcm.numcgm                                         \n";
    $stSql .= "          AND lclm.cod_norma    = lcm.cod_norma                                      \n";
    $stSql .= "   WHERE                                                                             \n";
    $stSql .= "         not exists( SELECT 1                                                        \n";
    $stSql .= "                       FROM licitacao.membro_excluido as lme                         \n";
    $stSql .= "                      WHERE lme.cod_comissao = lcm.cod_comissao                      \n";
    $stSql .= "                        AND lme.numcgm       = lcm.numcgm                            \n";
    $stSql .= "                        AND lme.cod_norma    = lcm.cod_norma )                       \n";
    $stSql .= "     AND lc.cod_comissao = ".$cod_comissao."                                         \n";
    $stSql .= "     AND lclm.cod_modalidade = ".$cod_modalidade."                                   \n";
    $stSql .= "     AND lclm.cod_licitacao = ".$cod_licitacao."                                     \n";

    return $stSql;
}

function recuperaMembrosNaoExcluidos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMembrosNaoExcluidos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMembrosNaoExcluidos()
{
    $stSql = "
                SELECT
                        comissao_membros.cod_comissao,
                        comissao_membros.numcgm,
                        comissao_membros.cod_norma
                  FROM
                        licitacao.comissao_membros
                 WHERE
                        comissao_membros.cod_comissao = ".$this->getDado('cod_comissao')."
        AND	NOT EXISTS	(
                            SELECT
                                    1
                              FROM
                                    licitacao.membro_excluido
                             WHERE
                                    membro_excluido.cod_norma = comissao_membros.cod_norma
                               AND	membro_excluido.numcgm = comissao_membros.numcgm
                               AND	membro_excluido.cod_comissao = comissao_membros.cod_comissao
                        )

    ";

    return $stSql;
}

function recuperaMembrosComissaoEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaMembrosComissaoEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaMembrosComissaoEsfinge()
{
    $stSql = "
                select norma.dt_assinatura
                , comissao.cod_comissao
                , sw_cgm_pessoa_fisica.cpf
                , norma_membros.dt_assinatura as dt_assinatura_membro
                , sw_cgm.nom_cgm
                , norma.num_norma
                , norma_data_termino.dt_termino
                , case when comissao_membros.cod_tipo_membro = 2 then 's'
                    else 'n'
                end as indicativo_presidencia
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

                join licitacao.comissao_membros
                on comissao_membros.cod_comissao = comissao.cod_comissao

                left join licitacao.membro_excluido
                on membro_excluido.cod_comissao = comissao_membros.cod_comissao
                and membro_excluido.numcgm = comissao_membros.numcgm
                and membro_excluido.cod_norma = comissao_membros.cod_norma

                join normas.norma as norma_membros
                on norma_membros.cod_norma = comissao_membros.cod_norma

                join normas.norma_data_termino
                on norma_data_termino.cod_norma = norma_membros.cod_norma

                join sw_cgm
                on sw_cgm.numcgm = comissao_membros.numcgm

                join sw_cgm_pessoa_fisica
                on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                where membro_excluido.cod_comissao is null

                and licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
                and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
                and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
                and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
    ";

    return $stSql;
}

}
