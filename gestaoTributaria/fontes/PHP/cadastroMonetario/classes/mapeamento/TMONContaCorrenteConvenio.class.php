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
  * Classe de mapeamento de Conta Corrente Convênio
  * Data de criação : 09/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TMONContaCorrenteConvenio.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.03, uc-05.05.04
**/

/*
$Log$
Revision 1.11  2007/03/08 15:22:24  rodrigo
Bug #8418#

Revision 1.10  2007/02/07 15:56:19  cercato
alteracoes para o convenio trabalhar com numero de variacao.

Revision 1.9  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONContaCorrenteConvenio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONContaCorrenteConvenio()
{
    parent::Persistente();
    $this->setTabela('monetario.conta_corrente_convenio');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_conta_corrente,cod_agencia,cod_banco,cod_convenio');

    $this->AddCampo('cod_conta_corrente','integer',true,'',true,true);
    $this->AddCampo('cod_agencia'       ,'integer',true,'',true,true);
    $this->AddCampo('cod_banco'         ,'integer',true,'',true,true);
    $this->AddCampo('cod_convenio'      ,'integer',true,'',true,true);
    $this->AddCampo('variacao'          ,'integer',false,'',false,false);
}

}
