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
     * Classe de mapeamento para a tabela IMOBILIARIO.ATRIBUTO_FACE_QUADRA_VALOR
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMAtributoFaceQuadraValor.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoCadastroImobiliario.class.php");
include_once ( CLA_PERSISTENTE_ATRIBUTOS_VALORES );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.ATRIBUTO_FACE_QUADRA_VALOR
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMAtributoFaceQuadraValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMAtributoFaceQuadraValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('imobiliario.atributo_face_quadra_valor');
    //$this->setPersistenteAtributo( new TCIMAtributoCadastroImobiliario );

    $this->setCampoCod('');
    $this->setComplementoChave('cod_face,cod_localizacao,cod_atributo,cod_cadastro,timestamp,cod_modulo');

    $this->AddCampo('cod_face','integer',true,'',true,true);
    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_localizacao','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('valor','varchar',true,'500',false,false);

}
}
