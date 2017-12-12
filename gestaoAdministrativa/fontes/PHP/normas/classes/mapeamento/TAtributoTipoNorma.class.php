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
* Classe de Mapeamento para tabela tipo_norma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  SW_ATRIBUTO_TIPO_NORMA
  * Data de Criação: 26/05/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TAtributoTipoNorma extends PersistenteAtributos
{
/**
    * Método Construtor
    * @access Private
*/
function TAtributoTipoNorma()
{
    parent::PersistenteAtributos();
    $this->setTabela('normas.atributo_tipo_norma');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atributo,cod_cadastro,cod_modulo,cod_tipo_norma');

    $this->AddCampo('cod_atributo'   ,'integer',true,'',true,true);
    $this->AddCampo('cod_cadastro'   ,'integer',true,'',true,true);
    $this->AddCampo('cod_modulo'   ,'integer',true,'',true,true);
    $this->AddCampo('cod_tipo_norma'   ,'integer',true,'',true,true);
    $this->AddCampo('ativo'          ,'boolean',true,'',false,false);

}

}
