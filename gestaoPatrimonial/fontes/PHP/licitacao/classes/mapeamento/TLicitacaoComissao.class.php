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
    * Classe de mapeamento da tabela licitacao.comissao
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19591 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-01-24 13:23:10 -0200 (Qua, 24 Jan 2007) $

    * Casos de uso: uc-03.05.09
*/
/*
$Log$
Revision 1.10  2007/01/24 15:18:37  hboaventura
Correção do layout da lista

Revision 1.9  2007/01/15 10:58:58  bruce
Bug #7824# simpesmente apaguei o metodo recuperaRelacionamento da classe TLicitacaoComissao, que estava errado e a classe passou a usar o metodo da persistente

Revision 1.8  2006/12/27 17:49:21  rodrigo
7824

Revision 1.7  2006/12/04 12:41:40  hboaventura
bug #7724#

Revision 1.6  2006/10/16 17:36:33  domluc
Alteração para uso no Componente de Comissao

Revision 1.5  2006/09/25 14:22:17  bruce
colocado o UC

Revision 1.4  2006/09/22 17:56:28  bruce
alterção de comissões

Revision 1.3  2006/09/22 11:11:09  bruce
criado metodo montaRecuperaRelacionamento

Revision 1.2  2006/09/21 09:42:02  bruce
setadas as chaves estrangeiras

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.comissao
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoComissao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoComissao()
{
    parent::Persistente();
    $this->setTabela("licitacao.comissao");

    $this->setCampoCod('cod_comissao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_comissao'     ,'sequence',false ,'',true,false);
    $this->AddCampo('cod_tipo_comissao','integer' ,false ,'',false,'TLicitacaoTipoComissao' );
    $this->AddCampo('cod_norma'        ,'integer' ,false ,'',false,'TNormasNorma');
    $this->AddCampo('ativo'            ,'boolean' ,true  ,'',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql = "Select comissao.cod_comissao,                                                                     \n " ;
    $stSql .= "       comissao.cod_tipo_comissao,                                                                \n " ;
    $stSql .= "       tipo_comissao.descricao as finalidade,                                                     \n " ;
    $stSql .= "       comissao.cod_norma,                                                                        \n " ;
    $stSql .= "       to_char(norma.dt_publicacao,'dd/mm/yyyy') as dt_publicacao,                                \n " ;
    $stSql .= "       to_char(norma_data_termino.dt_termino,'dd/mm/yyyy') as dt_termino,                         \n " ;
    $stSql .= "       norma.exercicio,                                                                           \n " ;
    $stSql .= "       norma.num_norma,                                                                           \n " ;
    $stSql .= "       ntn.nom_tipo_norma,                                                                        \n " ;
    $stSql .= "       CASE                                                                                       \n " ;
    $stSql .= "             WHEN ativo = true                                                                    \n " ;
    $stSql .= "             THEN 'Ativa' ELSE 'Inativa'                                                          \n " ;
    $stSql .= "       END AS status                                                                             \n " ;
    $stSql .= "       ,( select sw_cgm.nom_cgm                                                                    \n " ;
    $stSql .= "           from licitacao.comissao_membros                                                        \n " ;
    $stSql .= "           join sw_cgm                                                                            \n " ;
    $stSql .= "             on ( comissao_membros.numcgm = sw_cgm.numcgm )                                       \n " ;
    $stSql .= "          where comissao_membros.cod_tipo_membro in ( 2 )                                         \n " ;
    $stSql .= "            and comissao_membros.cod_comissao = comissao.cod_comissao                             \n " ;
    $stSql .= "            and not exists                                                                        \n " ;
    $stSql .= "                (select 1                                                                         \n " ;
    $stSql .= "                   from licitacao.membro_excluido                                                 \n " ;
    $stSql .= "                  where comissao_membros.numcgm = membro_excluido.numcgm                          \n " ;
    $stSql .= "                    and   comissao.cod_comissao = membro_excluido.cod_comissao) limit 1 ) as presidente   \n " ;
    $stSql .= "from licitacao.comissao                                                                           \n " ;
    $stSql .= "join licitacao.tipo_comissao                                                                      \n " ;
    $stSql .= "    on (tipo_comissao.cod_tipo_comissao = comissao.cod_tipo_comissao)                             \n " ;
    $stSql .= "join normas.norma                                                                                 \n " ;
    $stSql .= "    on (norma.cod_norma = comissao.cod_norma)                                                     \n " ;
    $stSql .= "left join normas.norma_data_termino                                                               \n " ;
    $stSql .= "    on (norma_data_termino.cod_norma = norma.cod_norma)                                           \n " ;
    $stSql .= "left join normas.tipo_norma ntn                                                              \n " ;
    $stSql .= "    on (norma.cod_tipo_norma = ntn.cod_tipo_norma)                                           \n " ;

    return $stSql;
}

function recuperaDataTerminoComissao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaDataTerminoComissao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaDataTerminoComissao()
{
    $stSql = " SELECT to_char(dt_termino,'dd/mm/yyyy') as dt_termino                                    \n";
    $stSql.= "   FROM licitacao.comissao_licitacao                                                      \n";
    $stSql.= "      , licitacao.comissao                                                                \n";
    $stSql.= "      , normas.norma                                                                      \n";
    $stSql.= "      , normas.norma_data_termino                                                         \n";
    $stSql.= "  WHERE comissao_licitacao.cod_comissao = comissao.cod_comissao                           \n";
    $stSql.= "    AND comissao.cod_norma = norma.cod_norma                                              \n";
    $stSql.= "    AND norma.cod_norma = norma_data_termino.cod_norma                                    \n";

    if ($this->getDado('cod_licitacao') ) {
        $stSql.= "    AND comissao_licitacao.cod_licitacao = ".$this->getDado('cod_licitacao')."        \n";
    }
    if ($this->getDado('cod_entidade') ) {
        $stSql.= "    AND comissao_licitacao.cod_entidade = ".$this->getDado('cod_entidade')."          \n";
    }
    if ($this->getDado('exercicio') ) {
        $stSql.= "    AND comissao_licitacao.exercicio = '".$this->getDado('exercicio')."'              \n";
    }
    if ($this->getDado('cod_modalidade') ) {
        $stSql.= "    AND comissao_licitacao.cod_modalidade = ".$this->getDado('cod_modalidade')."      \n";
    }

    return $stSql;
}

function recuperaComissoesCombo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaComissoesCombo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaComissoesCombo()
{
    $stSql = "Select comissao.cod_comissao,                                                                     \n " ;
    $stSql .= "       comissao.cod_tipo_comissao,                                                                \n " ;
    $stSql .= "       tipo_comissao.descricao as finalidade,                                                     \n " ;
    $stSql .= "       comissao.cod_norma,                                                                        \n " ;
    $stSql .= "       to_char(norma.dt_publicacao,'dd/mm/yyyy') as dt_publicacao,                                \n " ;
    $stSql .= "       to_char(norma_data_termino.dt_termino,'dd/mm/yyyy') as dt_termino,                         \n " ;
    $stSql .= "       norma.exercicio,                                                                           \n " ;
    $stSql .= "       norma.num_norma,                                                                           \n " ;
    $stSql .= "       ntn.nom_tipo_norma,                                                                        \n " ;
    $stSql .= "       CASE                                                                                       \n " ;
    $stSql .= "             WHEN ativo = true                                                                    \n " ;
    $stSql .= "             THEN 'Ativa' ELSE 'Inativa'                                                          \n " ;
    $stSql .= "       END AS status                                                                              \n " ;
    $stSql .= "from licitacao.comissao                                                                           \n " ;
    $stSql .= "join licitacao.tipo_comissao                                                                      \n " ;
    $stSql .= "    on (tipo_comissao.cod_tipo_comissao = comissao.cod_tipo_comissao)                             \n " ;
    $stSql .= "join normas.norma                                                                                 \n " ;
    $stSql .= "    on (norma.cod_norma = comissao.cod_norma)                                                     \n " ;
    $stSql .= "left join normas.norma_data_termino                                                               \n " ;
    $stSql .= "    on (norma_data_termino.cod_norma = norma.cod_norma)                                           \n " ;
    $stSql .= "left join normas.tipo_norma ntn                                                              \n " ;
    $stSql .= "    on (norma.cod_tipo_norma = ntn.cod_tipo_norma)                                           \n " ;

    return $stSql;
}

}
