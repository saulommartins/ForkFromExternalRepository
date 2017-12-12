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
    * Classe de mapeamento da tabela ARRECADACAO.LOTE
    * Data de Criação: 05/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo B. Paulino
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRLoteInconsistencia.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.3  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.2  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.LOTE_INSCONSISTENCIA
  * Data de Criação: 05/12/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Marcelo B. Paulino

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRLoteInconsistencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRLoteInconsistencia()
{
    parent::Persistente();
    $this->setTabela( "arrecadacao.lote_inconsistencia" );

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('exercicio,ocorrencia');

    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('ocorrencia','integer',true,'4',true,false);
    $this->AddCampo('numeracao','char',true,'',false,false);
    $this->AddCampo('data_pagamento','date',true,'',false,false);
    $this->AddCampo('valor','numeric',true,'',false,false);
}
}
?>
