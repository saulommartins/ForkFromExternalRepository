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
    * Página de mapeamento para busca dos dados arquivo Codigo_VantagensDescontos
    * Data de Criação   : 09/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTPBCodigoVantagensDescontos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBCodigoVantagensDescontos()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql  = " SELECT *                                                                \n";
    $stSql .= "   FROM tcepb.recuperaCodigoVantagensDescontos('".$this->getDado('stSchemaEntidade')."', '".$this->getDado('stMes')."') AS (        \n";
    $stSql .= "           cod_vantagem_desconto   VARCHAR                               \n";
    $stSql .= "         , nome_vantagem_desconto  VARCHAR                               \n";
    $stSql .= "         , tipo_lancamento         INTEGER                               \n";
    $stSql .= "         , tipo_contabilizacao     INTEGER                               \n";
    $stSql .= " );                                                                      \n";

    return $stSql;
}
}
