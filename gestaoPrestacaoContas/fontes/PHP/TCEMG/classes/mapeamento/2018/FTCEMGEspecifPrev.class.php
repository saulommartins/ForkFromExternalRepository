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
    * Arquivo de mapeamento para a função que busca os dados do arquivo receitaPrev
    * Data de Criação   : 03/02/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id: FTCEMGEspecifPrev.class.php 62821 2015-06-24 14:24:21Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGEspecifPrev extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGEspecifPrev()
{
    parent::Persistente();

    $this->setTabela('tcemg.especifprev');

    $this->AddCampo('stExercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('dtInicio'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('dtFinal'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('stEntidades'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('stRpps'          ,'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "

        SELECT ".$this->getDado('stMes')." AS mes
             , CASE WHEN aplicacoes_financeiras < 0.00
                    THEN aplicacoes_financeiras*-1
                    ELSE aplicacoes_financeiras
                END AS aplicacoes_financeiras
             , CASE WHEN caixa < 0.00
                    THEN caixa*-1
                    ELSE caixa
                END AS caixa
             , CASE WHEN banco < 0.00
                    THEN banco*-1
                    ELSE banco
                END AS banco
          FROM ".$this->getTabela()."( '".$this->getDado("stExercicio")."'
                                     , '".$this->getDado("dtInicio")."'
                                     , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stEntidades")."'
                                     ) AS retorno(
                                                   aplicacoes_financeiras   NUMERIC
                                                 , caixa                    NUMERIC
                                                 , banco                    NUMERIC
                                                 )";
    return $stSql;
}

}
