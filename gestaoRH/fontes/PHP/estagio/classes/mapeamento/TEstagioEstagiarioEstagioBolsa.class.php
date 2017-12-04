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
    * Classe de mapeamento da tabela estagio.estagiario_estagio_bolsa
    * Data de Criação : 15/02/2007

    * @author Desenvolvedor: Alexandre Melo

    * Casos de uso: uc-04.07.01

    $Id: TEstagioEstagiarioEstagioBolsa.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Efetua conexão com a tabela  estagio.estagiario_estagio_bolsa
  * Data de Criação: 15/02/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Alexandre Melo

  * @package URBEM
  * @subpackage Mapeamento
*/

class TEstagioEstagiarioEstagioBolsa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEstagioEstagiarioEstagioBolsa()
{
    parent::Persistente();
    $this->setTabela("estagio.estagiario_estagio_bolsa");

    $this->setCampoCod('');
    $this->setComplementoChave('cgm_instituicao_ensino,cgm_estagiario,cod_curso,cod_estagio,timestamp');

    $this->AddCampo('cgm_instituicao_ensino','integer',true  ,'',true  ,'TEstagioEstagiarioEstagio');
    $this->AddCampo('cgm_estagiario'        ,'integer',true  ,'',true  ,'TEstagioEstagiarioEstagio');
    $this->AddCampo('cod_curso'             ,'integer',true  ,'',true  ,'TEstagioEstagiarioEstagio');
    $this->AddCampo('cod_estagio'           ,'integer',true  ,'',true  ,'TEstagioEstagiarioEstagio');
    $this->AddCampo('timestamp'             ,'timestamp',false  ,'',true  ,false);
    $this->AddCampo('cod_periodo_movimentacao','integer',true  ,'',false  ,'TFolhaPagamentoPeriodoMovimentacao');
    $this->AddCampo('vl_bolsa'              ,'integer',true  ,'',false ,false);
    $this->AddCampo('faltas'                ,'integer',false ,'',false ,false);
    $this->AddCampo('vale_refeicao'         ,'boolean',false ,'',false ,false);
    $this->AddCampo('vale_transporte'       ,'boolean',false ,'',false ,false);
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT estagiario_estagio_bolsa.*                                                                           \n";
    $stSql .= "  FROM estagio.estagiario_estagio_bolsa                                           \n";
    $stSql .= "     , (  SELECT cod_estagio                                                                                \n";
    $stSql .= "               , cod_curso                                                                                  \n";
    $stSql .= "               , cgm_estagiario                                                                             \n";
    $stSql .= "               , cgm_instituicao_ensino                                                                     \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                \n";
    $stSql .= "            FROM estagio.estagiario_estagio_bolsa                                 \n";
    $stSql .= "        GROUP BY cod_estagio                                                                                \n";
    $stSql .= "               , cod_curso                                                                                  \n";
    $stSql .= "               , cgm_estagiario                                                                             \n";
    $stSql .= "               , cgm_instituicao_ensino) AS max_estagiario_estagio_bolsa                                    \n";
    $stSql .= "     , estagio.estagiario_estagio                                                 \n";
    $stSql .= " WHERE estagiario_estagio_bolsa.cod_estagio            = max_estagiario_estagio_bolsa.cod_estagio           \n";
    $stSql .= "   AND estagiario_estagio_bolsa.cod_curso              = max_estagiario_estagio_bolsa.cod_curso             \n";
    $stSql .= "   AND estagiario_estagio_bolsa.cgm_estagiario         = max_estagiario_estagio_bolsa.cgm_estagiario        \n";
    $stSql .= "   AND estagiario_estagio_bolsa.cgm_instituicao_ensino = max_estagiario_estagio_bolsa.cgm_instituicao_ensino\n";
    $stSql .= "   AND estagiario_estagio_bolsa.timestamp = max_estagiario_estagio_bolsa.timestamp                          \n";
    $stSql .= "   AND estagiario_estagio_bolsa.cod_estagio = estagiario_estagio.cod_estagio                                \n";
    $stSql .= "   AND estagiario_estagio_bolsa.cod_curso = estagiario_estagio.cod_curso                                    \n";
    $stSql .= "   AND estagiario_estagio_bolsa.cgm_estagiario = estagiario_estagio.cgm_estagiario                          \n";
    $stSql .= "   AND estagiario_estagio_bolsa.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino          \n";

    return $stSql;
}
}
?>
