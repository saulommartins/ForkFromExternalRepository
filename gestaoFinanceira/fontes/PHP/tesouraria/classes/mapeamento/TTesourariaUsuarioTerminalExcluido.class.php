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
    * Classe de mapeamento da tabela TESOURARIA_USUARIO_TERMINAL_EXCLUIDO
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
Revision 1.9  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_USUARIO_TERMINAL_EXCLUIDO
  * Data de Criação: 06/09/2005

  * @author Analista: Lucas Oiagen
  * @author Desenvolvedor: Cleisson da Silva Barboza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaUsuarioTerminalExcluido extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaUsuarioTerminalExcluido()
{
    parent::Persistente();
    $this->setTabela("tesouraria.usuario_terminal_excluido");

    $this->setCampoCod('');
     $this->setComplementoChave('timestamp_usuario,timestamp_terminal,cod_terminal,cgm_usuario');

    $this->AddCampo('timestamp_usuario' , 'timestamp', true, '' , true , true  );
    $this->AddCampo('timestamp_terminal', 'timestamp', true, '' , true , true  );
    $this->AddCampo('cgm_usuario'       , 'integer'  , true, '' , true , true  );
    $this->AddCampo('cod_terminal'      , 'integer'  , true, '' , true , true  );
    $this->AddCampo('timestamp_excluido', 'timestamp', true, '' , false, false );
}

}
