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
    * Classe de mapeamento para MONETARIO.INDICADOR
    * Data de Criacao: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TMONIndicadorEconomico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.07
*/

/*
$Log$
Revision 1.5  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONIndicadorEconomico extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TMONIndicadorEconomico()
{
    parent::Persistente();
    $this->setTabela('monetario.indicador_economico');

    $this->setCampoCod('cod_indicador');
    $this->setComplementoChave('');

    $this->AddCampo('cod_indicador','integer',true,'',true,false);
    $this->AddCampo('abreviatura','varchar',true,'15',true,false);
    $this->AddCampo('descricao','varchar',true,'80',false,false);
    $this->AddCampo('precisao','integer',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                  \n";
    $stSql .= "     ie.cod_indicador,                   \n";
    $stSql .= "     ie.descricao,                       \n";
    $stSql .= "     ie.abreviatura,                     \n";
    $stSql .= "     ie.precisao                         \n";
    $stSql .= " FROM                                    \n";
    $stSql .= "     monetario.indicador_economico as ie \n";

return $stSql;

}

}// fecha classe de mapeamento
