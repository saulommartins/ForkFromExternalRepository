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
    * Classe de mapeamento da tabela ima.ocorrencia_cadastral_910
    * Data de Criação: 03/08/2009

    * @author Analista     : Dagiane
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TIMAOcorrenciaCadastral910 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAOcorrenciaCadastral910()
{
    parent::Persistente();
    $this->setTabela("ima.ocorrencia_cadastral_910");

    $this->setCampoCod('num_ocorrencia');
    $this->setComplementoChave('');

    $this->AddCampo('num_ocorrencia','sequence',true  ,''     ,true,false);
    $this->AddCampo('descricao'     ,'varchar' ,true  ,'180'  ,false,false);

}
}
?>
