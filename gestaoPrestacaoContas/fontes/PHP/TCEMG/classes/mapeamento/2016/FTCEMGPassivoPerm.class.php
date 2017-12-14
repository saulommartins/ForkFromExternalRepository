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

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGPassivoPerm extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGPassivoPerm()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_passivo_perm');

    $this->AddCampo('exercicio'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade' ,'varchar',false,''    ,false,false);
    $this->AddCampo('data_inicial' ,'integer',false,''    ,false,false);
    $this->AddCampo('data_final'   ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    
    $stSql  = "
        SELECT mes
             , ABS(valoremp)             as valoremp
             , ABS(valortransconcedidas) as valortransconcedidas
             , ABS(valorprovisaorpps)    as valorprovisaorpps
             , codtipo

    FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                , '".$this->getDado("cod_entidade")."'
                                , '".$this->getDado("data_inicial")."'
                                , '".$this->getDado("data_final")."'
                                ) AS retorno(
                                    mes                     INTEGER,
                                    valoremp                NUMERIC,
                                    valortransconcedidas    NUMERIC,
                                    valorprovisaorpps       NUMERIC,
                                    codtipo                 INTEGER
                                )";

    return $stSql;
}

}
