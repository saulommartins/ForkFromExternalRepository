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
* Classe de Mapeamento para tabela norma_detalhe
* Data de Criação: 07/01/20015

* @author Analista: Silvia
* @author Desenvolvedor: Lisiane Morais

$Revision:
$Name$
$Author:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';


class TTCEMGNormaDetalhe extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEMGNormaDetalhe()
{
    parent::Persistente();
    $this->setTabela('tcemg.norma_detalhe');

    $this->setCampoCod('cod_norma');
    $this->setComplementoChave('');
    $this->AddCampo('cod_norma'                       ,'integer',true,'',true,false);
    $this->AddCampo('tipo_lei_origem_decreto'         ,'integer',false,'',false,true);
    $this->AddCampo('tipo_lei_alteracao_orcamentaria' ,'integer',false,'',false,true);
}

}