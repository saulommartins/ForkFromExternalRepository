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
    * Classe de mapeamento da tabela ALMOXARIFADO.INVENTARIO_ANULACAO
    * Data de Criação: 24/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 1.9 $
    $Name:  $
    $Author: bruce $
    $Date: 2007/07/24 20:00:05 $

    * Casos de uso: uc-03.03.15
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela ALMOXARIFADO.INVENTARIO_ANULACAO
  * Data de Criação: 24/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Mapeamento
*/

class TAlmoxarifadoInventarioAnulacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoInventarioAnulacao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.inventario_anulacao');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_almoxarifado, cod_inventario');

    $this->AddCampo( 'exercicio'       , 'char'     ,  true,   '4',  true, 'TAlmoxarifadoInventario' );
    $this->AddCampo( 'cod_almoxarifado', 'integer'  ,  true,    '',  true, 'TAlmoxarifadoInventario' );
    $this->AddCampo( 'cod_inventario'  , 'integer'  ,  true,    '',  true, 'TAlmoxarifadoInventario' );
    $this->AddCampo( 'motivo'          , 'varchar'  ,  true, '160', false, false);

}

}

?>
