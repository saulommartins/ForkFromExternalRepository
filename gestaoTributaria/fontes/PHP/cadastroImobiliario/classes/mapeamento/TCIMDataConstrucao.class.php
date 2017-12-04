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
    * Efetua conexão com a tabela IMOBILIARI.DATA_CONSTRUCAO
    * Data de criação : 08/06/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Programador: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TCIMDataConstrucao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
**/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCIMDataConStrucao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMDataConstrucao()
{
    parent::Persistente();
    $this->setTabela('imobiliario.data_construcao');

    $this->setCampoCod('cod_construcao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_construcao','integer',true,'',true,false);
    $this->AddCampo('data_construcao','date',true,'',false,false);
}
}
