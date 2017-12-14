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
  * Efetua conexão com a tabela  ARRECADACAO.ATRIBUTO_ITBI_VALOR
  * Data de Criação: 09/10/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TARRAtributoITBIValor.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.21
*/

/*
$Log$
Revision 1.2  2006/10/10 15:11:52  cercato
correcao do mapeamento.

Revision 1.1  2006/10/10 09:41:50  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_ATRIBUTOS_VALORES );

class TARRAtributoITBIValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TARRAtributoITBIValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('arrecadacao.atributo_itbi_valor');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_municipal,timestamp,cod_modulo,cod_cadastro,cod_atributo');

    $this->AddCampo('inscricao_municipal', 'integer', true, '', true, true);
    $this->AddCampo('timestamp', 'timestamp', false, '', true, true);
    $this->AddCampo('cod_modulo', 'integer', true, '', true, true);
    $this->AddCampo('cod_cadastro', 'integer', true, '', true, true);
    $this->AddCampo('cod_atributo', 'integer', true, '', true, true);
    $this->AddCampo('valor', 'text', true, '', false, false);
}

}
?>
