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
    * Classe de mapeamento da tabela TESOURARIA.CONCILIACAO_LANCAMENTO_CONTABIL
    * Data de Criação: 07/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.3  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaConciliacaoLancamentoContabil extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaConciliacaoLancamentoContabil()
{
    parent::Persistente();
    $this->setTabela("tesouraria.conciliacao_lancamento_contabil");

    $this->setCampoCod('cod_plano');
    $this->setComplementoChave('exercicio,cod_lote,tipo,sequencia,exercicio_conciliacao,cod_entidade,tipo_valor,mes');

    $this->AddCampo('cod_plano'             , 'integer'  , true, ''     , true  , true    );
    $this->AddCampo('exercicio'             , 'varchar'  , true, '04'   , true  , true    );
    $this->AddCampo('exercicio_conciliacao' , 'varchar'  , true, '04'   , true  , true    );
    $this->AddCampo('cod_lote'              , 'integer'  , true, ''     , true  , true    );
    $this->AddCampo('tipo'                  , 'char'     , true, '01'   , true  , true    );
    $this->AddCampo('sequencia'             , 'integer'  , true, ''     , true  , true    );
    $this->AddCampo('cod_entidade'          , 'integer'  , true, ''     , true  , true    );
    $this->AddCampo('tipo_valor'            , 'char'     , true, '01'   , true  , true    );
    $this->AddCampo('mes'                   , 'integer'  , true, ''     , true  , true    );

}

}
