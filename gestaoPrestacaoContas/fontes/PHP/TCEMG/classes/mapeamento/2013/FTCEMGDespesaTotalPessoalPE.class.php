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
    * Arquivo de mapeamento para a função que busca os dados  de despesa do pessoal
    * Data de Criação   : 22/01/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    $Id: FTCEMGDespesaTotalPessoalPE.class.php 62558 2015-05-19 20:36:20Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaTotalPessoalPE extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGDespesaTotalPessoalPE()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_despesa_total_pessoal_pe');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);    
    $this->AddCampo('mes'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('data_inicial'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('data_final'    ,'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
 $stSql  = "
        SELECT ".$this->getDado('mes')." as mes
             , REPLACE(vencVantagens            ::TEXT, '.', '') AS vencVantagens
             , REPLACE(inativos                 ::TEXT, '.', '') AS inativos
             , REPLACE(pensionistas             ::TEXT, '.', '') AS pensionistas
             , REPLACE(salarioFamilia           ::TEXT, '.', '') AS salarioFamilia
             , REPLACE(subsPrefeito             ::TEXT, '.', '') AS subsPrefeito
             , REPLACE(subsVice                 ::TEXT, '.', '') AS subsVice
             , REPLACE(subsSecret               ::TEXT, '.', '') AS subsSecret
             , REPLACE(obrigPatronais           ::TEXT, '.', '') AS obrigPatronais
             , REPLACE(repassePatronal          ::TEXT, '.', '') AS repassePatronal
             , REPLACE(sentJudPessoal           ::TEXT, '.', '') AS sentJudPessoal
             , REPLACE(indenDemissao            ::TEXT, '.', '') AS indenDemissao
             , REPLACE(incDemVolunt             ::TEXT, '.', '') AS incDemVolunt
             , REPLACE(sentJudAnt               ::TEXT, '.', '') AS sentJudAnt
             , REPLACE(inatPensFontCustProp     ::TEXT, '.', '') AS inatPensFontCustProp
             , REPLACE(outrasDespesasPessoal    ::TEXT, '.', '') AS outrasDespesasPessoal
             , REPLACE(nadaDeclararPessoal      ::TEXT, '.', '') AS nadaDeclararPessoal
             , REPLACE(despExercAnt             ::TEXT, '.', '') AS despExercAnt
             , REPLACE(exclusaoDespAnteriores   ::TEXT, '.', '') AS exclusaoDespAnteriores
             , REPLACE(corrPerApurac            ::TEXT, '.', '') AS corrPerApurac
             , REPLACE(despCorres               ::TEXT, '.', '') AS despCorres
             , REPLACE(despAnteriores           ::TEXT, '.', '') AS despAnteriores
          FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                     , '".$this->getDado("cod_entidade")."'                                     
                                     , '".$this->getDado("data_inicial")."'
                                     , '".$this->getDado("data_final")."'
                                     ) AS retorno(
                                                  vencVantagens    NUMERIC,
                                                  inativos         NUMERIC,
                                                  pensionistas     NUMERIC,
                                                  salarioFamilia   NUMERIC,
                                                  subsPrefeito     NUMERIC,
                                                  subsVice         NUMERIC,
                                                  subsSecret       NUMERIC,
                                                  obrigPatronais   NUMERIC,
                                                  repassePatronal  NUMERIC,
                                                  sentJudPessoal   NUMERIC,
                                                  indenDemissao    NUMERIC,
                                                  incDemVolunt     NUMERIC,
                                                  sentJudAnt       NUMERIC,
                                                  inatPensFontCustProp   NUMERIC,
                                                  outrasDespesasPessoal  NUMERIC,
                                                  despExercAnt           NUMERIC,
                                                  exclusaoDespAnteriores NUMERIC,
                                                  corrPerApurac          NUMERIC,
                                                  despCorres             NUMERIC,
                                                  despAnteriores         NUMERIC,
                                                  nadaDeclararPessoal    VARCHAR
                                                 )";

    return $stSql;
     }
   }

