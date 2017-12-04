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
  * Classe de mapeamento da tabela ECONOMICO.PROCESSO_DOMICILIO_FISCAL
  * Data de Criação: 13/03/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMProcessoDomicilioFiscal.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.4  2007/07/26 21:54:42  rodrigo
Bug#9747#

Revision 1.3  2007/07/25 16:08:41  rodrigo
Bug#9747#

Revision 1.2  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.PROCESSO_DOMICILIO_FISCAL
  * Data de Criação: 13/03/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMProcessoDomicilioFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMProcessoDomicilioFiscal()
{
    parent::Persistente();
    $this->setTabela('economico.processo_domicilio_fiscal');

    $this->setCampoCod('');
    $this->setComplementoChave('timestamp,inscricao_economica,ano_exercicio,cod_processo');
    //timestamp,inscricao_economica,ano_exercicio,cod_processo,timestamp_proc(unico nao fk)
    //$this->AddCampo('timestamp','timestamp',true,'',true,true);
    $this->AddCampo('timestamp','',false,'',true,true);
    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('ano_exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_processo','integer',true,'',true,true);
    $this->AddCampo('timestamp_proc','timestamp',false,'',true,false);
}

}
