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
    * Classe de mapeamento da tabela pessoal.atributo_cargo_valor
    * Data de Criação: 18/08/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.04.06

    $Id: TPessoalAtributoCargoValor.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalAtributoCargoValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAtributoCargoValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela("pessoal.atributo_cargo_valor");

    $this->setCampoCod('cod_cargo');
    $this->setComplementoChave('cod_modulo,cod_cadastro,cod_atributo,timestamp');

    $this->AddCampo('cod_modulo'  ,'integer'      ,true  ,'',true,'TAdministracaoAtributoDinamico');
    $this->AddCampo('cod_cadastro','integer'      ,true  ,'',true,'TAdministracaoAtributoDinamico');
    $this->AddCampo('cod_atributo','integer'      ,true  ,'',true,'TAdministracaoAtributoDinamico');
    $this->AddCampo('cod_cargo'   ,'sequence'     ,true  ,'',true,false);
    $this->AddCampo('timestamp'   ,'timestamp_now',true  ,'',true,false);
    $this->AddCampo('valor'       ,'text'         ,true  ,'',false,false);

}
}
?>
