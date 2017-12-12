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
 * Efetua conexão com a tabela  ppa.macro_objetivos
 * Data de Criação   : 06/05/2009

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPPAMacroObjetivo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPPAMacroObjetivo()
{
    parent::Persistente();
    $this->setTabela('ppa.macro_objetivo');

    $this->setCampoCod('cod_macro');

    $this->AddCampo('cod_macro', 'integer'  , true, ''   , true , false);
    $this->AddCampo('cod_ppa'  , 'integer'  , true, ''   , false, true);
    $this->AddCampo('descricao', 'varchar'  , true, '120', false, false);
    //$this->AddCampo('timestamp', 'timestamp', true, ''   , false, false);

}

function listMacroObjetivo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $stSql = "
        SELECT macro_objetivo.cod_macro
             , macro_objetivo.descricao
             , ppa.cod_ppa
             , ppa.ano_inicio
             , ppa.ano_final
          FROM ppa.macro_objetivo
    INNER JOIN ppa.ppa
            ON macro_objetivo.cod_ppa = ppa.cod_ppa
    ";

    return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

}
