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
    * Classe de mapeamento da tabela TESOURARIA_TERMINAL_DESATIVADO
    * Data de Criação: 06/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.02
*/

/*
$Log$
Revision 1.8  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_TERMINAL_DESATIVADO
  * Data de Criação: 06/09/2005

  * @author Analista: Lucas Oiagen
  * @author Desenvolvedor: Cleisson da Silva Barboza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaTerminalDesativado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaTerminalDesativado()
{
    parent::Persistente();
    $this->setTabela("tesouraria.terminal_desativado");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_terminal,timestamp_terminal');

    $this->AddCampo('cod_terminal','integer'  ,true,''  , true , true       );
    $this->AddCampo('timestamp_terminal'   ,'timestamp',true,''  , true , true       );
    $this->AddCampo('timestamp_desativado' ,'timestamp'  ,true,'', false, false);
}

}
