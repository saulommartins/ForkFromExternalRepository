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
  * Classe de mapeamento da tabela DOCUMENTODINAMICO.TAG_BASE
  * Data de Criação: 08/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

Casos de uso: uc-01.01.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  DOCUMENTODINAMICO.TAG_BASE
  * Data de Criação: 08/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TDocumentoDinamicoTagBase extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDocumentoDinamicoTagBase()
{
    parent::Persistente();
    $this->setTabela('documentoDinamico.tag_base');

    $this->setCampoCod('cod_atributo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_atributo','integer',true,'',true,false);
    $this->AddCampo('cod_documento','integer',true,'',false,true);
    $this->AddCampo('cod_modulo','integer',true,'',false,true);
    $this->AddCampo('descricao','varchar',true,'40',false,false);
    $this->AddCampo('esquema','varchar',true,'40',false,false);
    $this->AddCampo('tabela','varchar',true,'50',false,false);
    $this->AddCampo('coluna','varchar',true,'50',false,false);

}
}
