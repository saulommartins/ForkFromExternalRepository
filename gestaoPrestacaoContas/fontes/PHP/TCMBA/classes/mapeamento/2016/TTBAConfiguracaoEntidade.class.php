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
    * Data de Criação: 20/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: TTBAConfiguracaoEntidade.class.php 62823 2015-06-24 17:22:01Z evandro $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTBAConfiguracaoEntidade extends TAdministracaoConfiguracaoEntidade
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAConfiguracaoEntidade()
{
    parent::TAdministracaoConfiguracaoEntidade();
    $this->setDado("exercicio",Sessao::getExercicio());
    $this->setDado("cod_modulo",45); /*verificar número gerado pelos DBAs*/
}

function recuperaCodigos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCodigos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaCodigos()
{
    $stSql  =" SELECT   ent.cod_entidade            \n";
    $stSql .="         ,cgm.nom_cgm                 \n";
    $stSql .="         ,ce.valor                    \n";
    $stSql .=" FROM     sw_cgm              cgm     \n";
    $stSql .="         JOIN                         \n";
    $stSql .="          orcamento.entidade  ent     \n";
    $stSql .="         ON (                         \n";
    $stSql .="             cgm.numcgm = ent.numcgm  \n";
    $stSql .="         )                            \n";
    $stSql .="         LEFT JOIN                    \n";
    $stSql .="          administracao.configuracao_entidade ce      \n";
    $stSql .="         ON (                                         \n";
    $stSql .="             ent.exercicio   = ce.exercicio           \n";
    $stSql .="         AND ent.cod_entidade= ce.cod_entidade        \n";
    $stSql .="         AND ce.cod_modulo  = ".$this->getDado('cod_modulo')."            \n";
    $stSql .="         AND ce.parametro   = 'tceba_codigo_unidade_gestora'              \n";
    $stSql .="         )                                                                \n";
    $stSql .=" WHERE   ent.exercicio = '".$this->getDado('exercicio')."'                 \n";

    return $stSql;
}

}
?>
