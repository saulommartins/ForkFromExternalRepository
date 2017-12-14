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
/*
	* Classe de mapeamento da tabela tcemg.contrato_aditivo_tipo
	* Data de Criação   : 25/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGContratoAditivoTipo.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGContratoAditivoTipo extends Persistente
{
/**
	* Método Construtor
	* @access Private
*/
function TTCEMGContratoAditivoTipo()
{
    parent::Persistente();
    $this->setTabela('tcemg.contrato_aditivo_tipo');
    
    $this->setCampoCod('');
    $this->setComplementoChave('cod_tipo_aditivo');
    
    $this->AddCampo( 'cod_tipo_aditivo', 'integer'  , true  , ''     , true , '');
    $this->AddCampo( 'descricao'       , 'varchar'  , true  , '80'   , true , '');
}

public function __destruct(){}

}

?>
