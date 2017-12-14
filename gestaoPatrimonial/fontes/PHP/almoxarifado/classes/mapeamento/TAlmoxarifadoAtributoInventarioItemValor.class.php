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
    * Classe de mapeamento
    * Data de Criação: 15/10/2008

    * @author Analista      : Diego Victoria
    * @author Desenvolvedor : Diego Victoria

    * @package URBEM
    * Casos de uso: uc-03.03.15
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAlmoxarifadoAtributoInventarioItemValor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
  public function TAlmoxarifadoAtributoInventarioItemValor()
  {
    parent::Persistente();
    $this->setTabela('almoxarifado.atributo_inventario_item_valor');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro, cod_modulo, cod_cadastro, cod_atributo');

    $this->AddCampo('exercicio'         ,'char'   ,true,'4',true,true );
    $this->AddCampo('cod_almoxarifado'  ,'integer',true,'' ,true,true );
    $this->AddCampo('cod_inventario'    ,'integer',true,'' ,true,true );
    $this->AddCampo('cod_item'          ,'integer',true,'' ,true,true );
    $this->AddCampo('cod_marca'         ,'integer',true,'' ,true,true );
    $this->AddCampo('cod_centro'        ,'integer',true,'' ,true,true );
    $this->AddCampo('cod_modulo'        ,'integer',true,'' ,true,true );
    $this->AddCampo('cod_cadastro'      ,'integer',true,'' ,true,true );
    $this->AddCampo('cod_atributo'      ,'integer',true,'' ,true,true );
    $this->AddCampo('valor'             ,'varchar',true,'1500',false,false );

    $this->setDado('cod_modulo', 29 );
  }

}
