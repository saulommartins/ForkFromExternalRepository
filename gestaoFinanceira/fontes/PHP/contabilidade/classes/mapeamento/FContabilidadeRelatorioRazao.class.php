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
    * Classe de mapeamento da tabela FN_CONTABILIDADE_RELATORIO_RAZAO
    * Data de Criação: 06/04/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32478 $
    $Name$
    $Author: bruce $
    $Date: 2007-06-19 17:19:14 -0300 (Ter, 19 Jun 2007) $

    * Casos de uso: uc-02.02.27
*/

/*
$Log$
Revision 1.12  2007/06/19 20:17:41  bruce
Correção da identificação de Bug

Revision 1.11  2007/06/19 14:58:36  gelson
Correção na forma do commit, deve ser:
Bug #numerodobug#

Revision 1.10  2007/04/11 21:34:13  luciano
Bug#8824#

Revision 1.9  2006/09/14 14:53:06  jose.eduardo
Bug #6832#

Revision 1.8  2006/08/23 17:03:13  jose.eduardo
Bug #6765#

Revision 1.7  2006/07/25 14:34:26  jose.eduardo
Bug #4343#

Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeRelatorioRazao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeRelatorioRazao()
{
    parent::Persistente();
    $this->setTabela('contabilidade.fn_relatorio_razao');

    $this->AddCampo('exercicio'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('filtro'                ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_estrutural_inicial','varchar',false,''    ,false,false);
    $this->AddCampo('cod_estrutural_final'  ,'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                   \n";
    $stSql .= "     *                                                                    \n";
    $stSql .= " FROM                                                                     \n";
    $stSql .= "   ".$this->getTabela()."('".$this->getDado("exercicio")              ."' \n";
    $stSql .= "                         ,'".$this->getDado("filtro")                 ."' \n";
    $stSql .= "                         ,'".$this->getDado("cod_estrutural_inicial") ."' \n";
    $stSql .= "                         ,'".$this->getDado("cod_estrutural_final")   ."' \n";
    $stSql .= "                         ,'".$this->getDado("stDtInicial")            ."' \n";
    $stSql .= "                         ,'".$this->getDado("stDtFinal")              ."' \n";
    $stSql .= "                         ,'".$this->getDado("stEntidade")             ."' \n";
    $stSql .= "                         ,'".$this->getDado("dtInicialAnterior")      ."' \n";
    $stSql .= "                         ,'".$this->getDado("dtFinalAnterior")        ."' \n";
    $stSql .= "                         ,'".$this->getDado("boMovimentacaoConta")    ."')\n";
    $stSql .= "     as retorno( cod_lote          integer                                \n";
    $stSql .= "                ,sequencia         integer                                \n";
    $stSql .= "                ,cod_historico     integer                                \n";
    $stSql .= "                ,nom_historico     varchar                                \n";
    $stSql .= "                ,complemento       varchar                                \n";
    $stSql .= "                ,observacao        text                                   \n";
    $stSql .= "                ,exercicio         char(4)                                \n";
    $stSql .= "                ,cod_entidade      integer                                \n";
    $stSql .= "                ,tipo              char(1)                                \n";
    $stSql .= "                ,vl_lancamento     numeric                                \n";
    $stSql .= "                ,tipo_valor        char(1)                                \n";
    $stSql .= "                ,dt_lote           varchar                                \n";
    $stSql .= "                ,cod_plano         integer                                \n";
    $stSql .= "                ,cod_estrutural    varchar                                \n";
    $stSql .= "                ,nom_conta         varchar                                \n";
    $stSql .= "                ,contra_partida    numeric                                \n";
    $stSql .= "                ,saldo_anterior    numeric                                \n";
    $stSql .= "                ,num_lancamentos   integer                                \n";
    $stSql .= "                )                                                         \n";

    return $stSql;
}

function recuperaRelatorioRazaoHistoricoCompleto(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelatorioRazaoHistoricoCompleto().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioRazaoHistoricoCompleto()
{
    $stSql  = " SELECT                                                                                                 \n";
    $stSql .= "     *                                                                                                  \n";
    $stSql .= " FROM                                                                                                   \n";
    $stSql .= "   contabilidade.fn_relatorio_razao_historico_completo ('".$this->getDado("exercicio")              ."' \n";
    $stSql .= "                         ,'".$this->getDado("filtro")                 ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("cod_estrutural_inicial") ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("cod_estrutural_final")   ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("stDtInicial")            ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("stDtFinal")              ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("stEntidade")             ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("dtInicialAnterior")      ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("dtFinalAnterior")        ."'                               \n";
    $stSql .= "                         ,'".$this->getDado("boMovimentacaoConta")    ."')                              \n";
    $stSql .= "     as retorno( cod_lote          integer                                                              \n";
    $stSql .= "                ,sequencia         integer                                                              \n";
    $stSql .= "                ,cod_historico     integer                                                              \n";
    $stSql .= "                ,nom_historico     varchar                                                              \n";
    $stSql .= "                ,complemento       varchar                                                              \n";
    $stSql .= "                ,exercicio         char(4)                                                              \n";
    $stSql .= "                ,cod_entidade      integer                                                              \n";
    $stSql .= "                ,tipo              char(1)                                                              \n";
    $stSql .= "                ,vl_lancamento     numeric                                                              \n";
    $stSql .= "                ,tipo_valor        char(1)                                                              \n";
    $stSql .= "                ,dt_lote           varchar                                                              \n";
    $stSql .= "                ,cod_plano         integer                                                              \n";
    $stSql .= "                ,cod_estrutural    varchar                                                              \n";
    $stSql .= "                ,nom_conta         varchar                                                              \n";
    $stSql .= "                ,contra_partida    numeric                                                              \n";
    $stSql .= "                ,saldo_anterior    numeric                                                              \n";
    $stSql .= "                ,num_lancamentos   integer                                                              \n";
    $stSql .= "                ,observacao        varchar                                                              \n";
    $stSql .= "                )                                                                                       \n";

    return $stSql;
}

}
