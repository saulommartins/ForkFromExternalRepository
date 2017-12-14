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
* Classe de mapeamento da tabela SW_ESCOLARIDADE
* Data de Criação: 24/10/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package URBEM
* @subpackage Mapeamento

$Revision: 4338 $
$Name$
$Author: souzadl $
$Date: 2005-12-27 11:38:13 -0200 (Ter, 27 Dez 2005) $

* Casos de uso: uc-01.02.94, uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela SW_ESCOLARIDADE
  * Data de Criação: 25/10/2005

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEscolaridade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEscolaridade()
{
    parent::Persistente();
    $this->setTabela( "sw_escolaridade" );

    $this->setCampoCod('cod_escolaridade');
    $this->setComplementoChave('');

    $this->AddCampo('cod_escolaridade', 'integer', true, '',  true, false);
    $this->AddCampo('descricao'       , 'string' , true, '', false, false);
}

}
