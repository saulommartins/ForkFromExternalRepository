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
    * Classe de mapeamento da tabela empenho.tipo_vinculo
    * Data de Criação: 28/02/2008

    * @author Analista: Tonismar
    * @author Desenvolvedor: Alexandre Melo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.37
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoTipoVinculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoTipoVinculo()
{
    parent::Persistente();
    $this->setTabela("empenho".Sessao::getEntidade().".tipo_vinculo");

    $this->setCampoCod('cod_tipo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_tipo' ,'sequence',true  ,''    ,true,false);
    $this->AddCampo('descricao','varchar' ,true  ,'40'  ,false,false);

}
}
?>
