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
* Classe de Mapeamento para tabela organograma_nivel
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.05.01, uc-01.05.02, uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORGANOGRAMA_NIVEL
  * Data de Criação: 16/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @package URBEM
  * @subpackage Mapeamento
*/
class TOrganogramaNivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrganogramaNivel()
{
    parent::Persistente();
    $this->setTabela('organograma.nivel');

    $this->setCampoCod('cod_nivel');
    $this->setComplementoChave('cod_organograma');

    $this->AddCampo('cod_nivel'      ,'integer',true,'',true,false);
    $this->AddCampo('cod_organograma','integer',true,'',true,true);
    $this->AddCampo('descricao'      ,'varchar',true,'100',false,false);
    $this->AddCampo('mascaracodigo'  ,'varchar',true,'10',false,false);
//    $this->AddCampo('num_nivel'      ,'integer',false,'',false,false);
}
}
