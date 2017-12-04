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
    * Classe de mapeamento da tabela estagio.estagiario_vale_refeicao
    * Data de Criação: 07/08/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.07.01

    $Id: TEstagioEstagiarioValeRefeicao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEstagioEstagiarioValeRefeicao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEstagioEstagiarioValeRefeicao()
{
    parent::Persistente();
    $this->setTabela("estagio.estagiario_vale_refeicao");

    $this->setCampoCod('');
    $this->setComplementoChave('cgm_instituicao_ensino,cgm_estagiario,cod_curso,cod_estagio,timestamp');

    $this->AddCampo('cgm_instituicao_ensino','integer'  ,true  ,''      ,true,'TEstagioEstagiarioEstagioBolsa');
    $this->AddCampo('cgm_estagiario'        ,'integer'  ,true  ,''      ,true,'TEstagioEstagiarioEstagioBolsa');
    $this->AddCampo('cod_curso'             ,'integer'  ,true  ,''      ,true,'TEstagioEstagiarioEstagioBolsa');
    $this->AddCampo('cod_estagio'           ,'integer'  ,true  ,''      ,true,'TEstagioEstagiarioEstagioBolsa');
    $this->AddCampo('timestamp'             ,'timestamp',true  ,''      ,true,'TEstagioEstagiarioEstagioBolsa');
    $this->AddCampo('quantidade'            ,'integer'  ,true  ,''      ,false,false);
    $this->AddCampo('vl_vale'               ,'numeric'  ,true  ,'14,2'  ,false,false);
    $this->AddCampo('vl_desconto'           ,'numeric'  ,true  ,'14,2'  ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "     SELECT estagiario_vale_refeicao.quantidade                                                                      \n";
    $stSql .= " 	 , to_real(estagiario_vale_refeicao.vl_vale) as vl_vale                                                     \n";
    $stSql .= "          , to_real(estagiario_vale_refeicao.vl_desconto) as vl_desconto                                             \n";
    $stSql .= "       FROM estagio.estagiario_vale_refeicao                                                                         \n";
    $stSql .= " INNER JOIN (  SELECT cod_estagio                                                                                    \n";
    $stSql .= " 		    , cod_curso                                                                                     \n";
    $stSql .= " 		    , cgm_estagiario                                                                                \n";
    $stSql .= " 		    , cgm_instituicao_ensino                                                                        \n";
    $stSql .= " 		    , max(timestamp) as timestamp                                                                   \n";
    $stSql .= " 		 FROM estagio.estagiario_vale_refeicao                                                              \n";
    $stSql .= "              GROUP BY cod_estagio                                                                                   \n";
    $stSql .= " 		    , cod_curso                                                                                     \n";
    $stSql .= " 		    , cgm_estagiario                                                                                \n";
    $stSql .= " 		    , cgm_instituicao_ensino                                                                        \n";
    $stSql .= " 	 ) AS max_estagiario_vale_refeicao                                                                          \n";
    $stSql .= " 	ON (estagiario_vale_refeicao.cod_estagio             = max_estagiario_vale_refeicao.cod_estagio             \n";
    $stSql .= "        AND estagiario_vale_refeicao.cod_curso                = max_estagiario_vale_refeicao.cod_curso               \n";
    $stSql .= "        AND estagiario_vale_refeicao.cgm_estagiario           = max_estagiario_vale_refeicao.cgm_estagiario          \n";
    $stSql .= "        AND estagiario_vale_refeicao.cgm_instituicao_ensino   = max_estagiario_vale_refeicao.cgm_instituicao_ensino  \n";
    $stSql .= "        AND estagiario_vale_refeicao.timestamp 		     = max_estagiario_vale_refeicao.timestamp)              \n";
    $stSql .= " INNER JOIN estagio.estagiario_estagio                                                                               \n";
    $stSql .= " 	ON (estagiario_vale_refeicao.cod_estagio 	     = estagiario_estagio.cod_estagio                       \n";
    $stSql .= "        AND estagiario_vale_refeicao.cod_curso 		     = estagiario_estagio.cod_curso                         \n";
    $stSql .= "        AND estagiario_vale_refeicao.cgm_estagiario 	     = estagiario_estagio.cgm_estagiario                    \n";
    $stSql .= "        AND estagiario_vale_refeicao.cgm_instituicao_ensino   = estagiario_estagio.cgm_instituicao_ensino)           \n";
    $stSql .= " INNER JOIN estagio.estagiario_estagio_bolsa                                                                         \n";
    $stSql .= " 	ON ( estagiario_estagio_bolsa.cgm_instituicao_ensino = estagiario_vale_refeicao.cgm_instituicao_ensino      \n";
    $stSql .= "        AND estagiario_estagio_bolsa.cgm_estagiario           = estagiario_vale_refeicao.cgm_estagiario              \n";
    $stSql .= "        AND estagiario_estagio_bolsa.cod_curso                = estagiario_vale_refeicao.cod_curso                   \n";
    $stSql .= "        AND estagiario_estagio_bolsa.cod_estagio              = estagiario_vale_refeicao.cod_estagio                 \n";
    $stSql .= "        AND estagiario_estagio_bolsa.timestamp                = estagiario_vale_refeicao.timestamp)                  \n";

    return $stSql;
}

function montaRecuperaValeRefeicaoPorPeriodoMovimentacao()
{
    $stSql .= "      SELECT estagiario_estagio_bolsa.cod_periodo_movimentacao                                                    \n";
    $stSql .= "	          , estagiario_vale_refeicao.timestamp                                                                   \n";
    $stSql .= "	       FROM estagio.estagiario_vale_refeicao                                                                     \n";
    $stSql .= " INNER  JOIN estagio.estagiario_estagio_bolsa                                                                     \n";
    $stSql .= "          ON ( estagiario_estagio_bolsa.cgm_instituicao_ensino = estagiario_vale_refeicao.cgm_instituicao_ensino  \n";
    $stSql .= "	        AND   estagiario_estagio_bolsa.cgm_estagiario         = estagiario_vale_refeicao.cgm_estagiario          \n";
    $stSql .= "	        AND   estagiario_estagio_bolsa.cod_curso              = estagiario_vale_refeicao.cod_curso               \n";
    $stSql .= "         AND   estagiario_estagio_bolsa.cod_estagio            = estagiario_vale_refeicao.cod_estagio             \n";
    $stSql .= "         AND   estagiario_estagio_bolsa.timestamp              = estagiario_vale_refeicao.timestamp )             \n";

    return $stSql;
}

function recuperaValeRefeicaoPorPeriodoMovimentacao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY estagiario_vale_refeicao.timestamp ";
    $stSql  = $this->montaRecuperaValeRefeicaoPorPeriodoMovimentacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
?>
