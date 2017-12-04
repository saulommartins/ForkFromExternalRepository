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
    * Classe de mapeamento da tabela ima.configuracao_convenio_bb
    * Data de Criação: 20/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alexandre Melo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.08.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.configuracao_convenio_bb
  * Data de Criação: 20/12/2006

  * @author Analista: Dagiane
  * @author Desenvolvedor: Alexandre Melo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConfiguracaoConvenioBb extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConfiguracaoConvenioBb()
{
    ;
    parent::Persistente();
    $this->setTabela("ima.configuracao_convenio_bb");

    $this->setCampoCod('cod_convenio');
    $this->setComplementoChave('cod_banco');

    $this->AddCampo('cod_convenio'      ,'sequence',true  ,''    ,true,false);
    $this->AddCampo('cod_convenio_banco','varchar' ,true  ,'20'  ,false,false);
    $this->AddCampo('cod_banco'         ,'integer' ,true  ,''    ,true,'TMONBanco');

}
}
?>
