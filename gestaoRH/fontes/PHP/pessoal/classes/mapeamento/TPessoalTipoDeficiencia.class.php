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
    * Classe de mapeamento da tabela pessoal.tipo_deficiencia
    * Data de Criação: 23/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.04.38

    $Id: TPessoalTipoDeficiencia.class.php 30566 2008-06-27 13:50:23Z domluc $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.tipo_deficiencia
  * Data de Criação: 23/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalTipoDeficiencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalTipoDeficiencia()
{
    parent::Persistente();
    $this->setTabela("pessoal.tipo_deficiencia");

    $this->setCampoCod('cod_tipo_deficiencia');
    $this->setComplementoChave('');

    $this->AddCampo('cod_tipo_deficiencia','sequence',true  ,''    ,true,false);
    $this->AddCampo('num_deficiencia'     ,'integer' ,true  ,''    ,false,false);
    $this->AddCampo('descricao'           ,'varchar' ,true  ,'20'  ,false,false);

}
}
?>
