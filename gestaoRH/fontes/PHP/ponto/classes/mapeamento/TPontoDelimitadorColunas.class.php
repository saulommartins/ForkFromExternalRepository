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
    * Classe de mapeamento da tabela ponto.delimitador_colunas
    * Data de Criação: 03/10/2008

    * @author Analista: Dagiane
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.10.12

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoDelimitadorColunas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoDelimitadorColunas()
{
    parent::Persistente();
    $this->setTabela("ponto.delimitador_colunas");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_formato,cod_campo');

    $this->AddCampo('cod_formato','integer',true  ,'',true,'TPontoFormatoDelimitador');
    $this->AddCampo('cod_campo'  ,'integer',true  ,'',true,'TPontoFormatoCampos');
    $this->AddCampo('coluna'     ,'integer',true  ,'',false,false);

}
}
?>
