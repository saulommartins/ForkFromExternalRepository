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
    * Classe de mapeamento da tabela
    * Data de Criação: 24/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOConfiguracaoEntidade.class.php 65208 2016-05-03 17:09:01Z lisiane $

    * Casos de uso: uc-06.04.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTGOConfiguracaoEntidade extends TAdministracaoConfiguracaoEntidade
{
/**
    * Método Construtor
    * @access Private
*/
function TTGOConfiguracaoEntidade()
{
    parent::TAdministracaoConfiguracaoEntidade();
    $this->setDado("exercicio",Sessao::getExercicio());
    $this->setDado("cod_modulo",0); /*verificar número gerado pelos DBAs*/
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
    $stSql .="         ,( SELECT    valor
                            FROM    administracao.configuracao_entidade
                           WHERE    configuracao_entidade.cod_entidade = ent.cod_entidade
                             AND    configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                             AND    configuracao_entidade.parametro = 'tc_codigo_tipo_balancete'
                             AND    configuracao_entidade.cod_modulo  = ".$this->getDado('cod_modulo')."
                        ) as tipo_balancete         \n";
    $stSql .="         ,( SELECT  CASE WHEN LENGTH(valor) >= 11 THEN SUBSTR(valor, 1, LENGTH(valor)-11)
                            ELSE valor
                             end AS valor

                            FROM    administracao.configuracao_entidade
                           WHERE    configuracao_entidade.cod_entidade = ent.cod_entidade
                             AND    configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                             AND    configuracao_entidade.parametro = 'tc_ug_orgaounidade'
                        ) as orgao_unidade          \n";
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
    $stSql .="         AND ce.parametro   = '".$this->getDado('parametro')."'              \n";
    $stSql .="         )                                                                \n";
    $stSql .=" WHERE   ent.exercicio = '".$this->getDado('exercicio')."'                  \n";

    return $stSql;
}

function recuperaEntidadePrefeitura(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEntidadePrefeitura().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaEntidadePrefeitura()
{
    $stSql  = " SELECT ent.cod_entidade            
                     , cgm.nom_cgm                 
                 FROM sw_cgm  cgm     
                 JOIN orcamento.entidade  ent     
                   ON cgm.numcgm = ent.numcgm  
                WHERE ent.exercicio = '".$this->getDado('exercicio')."'                 
                  AND  ent.cod_entidade = (SELECT valor::INTEGER
                                                  FROM administracao.configuracao
                                                 WHERE configuracao.parametro = 'cod_entidade_prefeitura'
                                                   AND exercicio = '".$this->getDado('exercicio')."' ) ";
   return $stSql;
}  


}
