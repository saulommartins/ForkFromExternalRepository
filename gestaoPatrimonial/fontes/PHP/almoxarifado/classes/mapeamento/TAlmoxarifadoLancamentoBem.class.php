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
  * Arquivo de Mapeamento da tabela Lancamento_bem
  * Data de criação : 08/12/2008

    * @author Analista: Gelson W
    * @author Programador: Luiz Felipe Prestes Teixeira

    $Id: $

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAlmoxarifadoLancamentoBem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TAlmoxarifadoLancamentoBem()
    {
        parent::Persistente();

        $this->setTabela('almoxarifado.lancamento_bem');

        $this->setCampoCod('cod_lancamento');
        $this->setComplementoChave('cod_item','cod_marca','cod_almoxarifado','cod_centro','cod_bem');

        $this->AddCampo('cod_lancamento','integer',true,'',true,true);
        $this->AddCampo('cod_bem','integer',true,'',true,true);
        $this->AddCampo('cod_item','integer',true,'',true,true);
        $this->AddCampo('cod_marca','integer',true,'',true,true);
        $this->AddCampo('cod_centro','integer',true,'',true,true);
        $this->AddCampo('cod_almoxarifado','integer',true,'',true,true);
    }
}
