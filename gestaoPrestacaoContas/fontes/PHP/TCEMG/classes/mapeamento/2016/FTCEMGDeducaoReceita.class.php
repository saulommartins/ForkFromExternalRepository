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
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 04/02/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage
    * 
    * $Id: FTCEMGDeducaoReceita.class.php 63326 2015-08-18 17:43:41Z franver $
    * $Date: 2015-08-18 14:43:41 -0300 (Ter, 18 Ago 2015) $
    * $Rev: 63326 $
    * $Author: franver $
    * 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDeducaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGDeducaoReceita()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_deducao_receita');

    $this->AddCampo('exercicio'    , 'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade' , 'varchar',false,''    ,false,false);
    $this->AddCampo('dt_inicial'   , 'varchar',false,''    ,false,false);
    $this->AddCampo('dt_final'     , 'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
            SELECT
                    mes --fixo 12
                    ,cod_tipo
                    ,REPLACE(valor,'.','') as valor
            FROM tcemg.fn_deducao_receita(
                                           '".$this->getDado("exercicio")."'
                                           ,'".$this->getDado("dt_inicial")."'
                                           ,'".$this->getDado("dt_final")."'
                                           ,'".$this->getDado("cod_entidade")."'
                                           )
            as retorno(
                mes         varchar
                ,cod_tipo   varchar
                ,valor      varchar
            )

      ";

    return $stSql;
}

}
