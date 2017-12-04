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
  * Classe de mapeamento da tabela ECONOMICO.ATRIBUTO_ELEM_LICEN_DIVERSA_VALOR
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtributoElemLicenDiversaValor.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.6  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoCadastroEconomico.class.php");
include_once ( CLA_PERSISTENTE_ATRIBUTOS_VALORES );

/**
  * Efetua conexão com a tabela  ECONOMICO.ATRIBUTO_ELEM_LICEN_DIVERSA_VALOR
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMAtributoElemLicenDiversaValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtributoElemLicenDiversaValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('economico.atributo_elem_licen_diversa_valor');
    $this->setPersistenteAtributo(new TCEMAtributoElemento);

    $this->setCampoCod('');
    $this->setComplementoChave('cod_elemento,cod_tipo,cod_licenca,exercicio,ocorrencia,cod_atributo,cod_cadastro,timestamp');

    $this->AddCampo('cod_elemento','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_licenca','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('ocorrencia','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('valor','varchar',true,'500',false,false);
    $this->AddCampo('cod_modulo','integer',true,'',true,true);

}
}
