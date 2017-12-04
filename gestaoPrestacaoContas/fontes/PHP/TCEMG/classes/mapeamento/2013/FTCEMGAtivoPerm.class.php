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

class FTCEMGAtivoPerm extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGAtivoPerm()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_ativo_perm');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
        SELECT mes
             , REPLACE(valorbensmov::VARCHAR,'.','') AS valorbensmov
             , REPLACE(valorbensimo::VARCHAR ,'.','') AS valorbensimo
             , REPLACE(valorobrasinst::VARCHAR ,'.','') AS valorobrasinst
             , REPLACE(valortitval::VARCHAR ,'.','') AS valortitval
             , REPLACE(valordivativa::VARCHAR ,'.','') AS valordivativa
             , REPLACE(valortransrecebidas::VARCHAR ,'.','') AS valortransrecebidas
             , REPLACE(valorreversaorpps::VARCHAR ,'.','') AS valorreversaorpp
             , codtipo

          FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                     , '".$this->getDado("cod_entidade")."'
                                     , ".$this->getDado("mes")."
                                     ) AS retorno(
                                                  mes                 INTEGER,
                                                  valorbensmov        NUMERIC,
                                                  valorbensimo        NUMERIC,
                                                  valorobrasinst      NUMERIC,
                                                  valortitval         NUMERIC,
                                                  valordivativa       NUMERIC,
                                                  valortransrecebidas NUMERIC,
                                                  valorreversaorpps   NUMERIC,
                                                  codtipo             INTEGER
                                                 )";

    return $stSql;
}

}
