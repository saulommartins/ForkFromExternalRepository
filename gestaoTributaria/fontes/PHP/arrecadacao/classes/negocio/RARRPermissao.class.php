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
    * Classe de regra de negócio para ARRECADACAO.PERMISSAO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: RARRPermissao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.8  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.7  2006/09/15 10:48:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPermissao.class.php"    );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"             );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"             );
/**
    * Classe de regra de negócio para arrecadacao grupo
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra
*/

class RARRPermissao
{
/**
    * @access private
    * @var Object
*/
var $obTARRPermissao;
/**
    * @access Private
    * @var Object
*/
var $obRARRGrupo;
/**
    * @access Private
    * @var Object
*/
var $obRCGM;
/**
    * @access Private
    * @var Boolean
*/
var $boPermitido;

// Getteres
/**
     * @access Public
     * @return String
 */
function getPermitido() { return $this->boPermitido  ; }

// Setteres
/**
     * @access Public
     * @return String
 */
function setPermitido($value) { $this->boPermitido = $value ; }

/**
     * Método construtor
     * @access Private
*/
function RARRPermissao()
{
    // mapeamento
    $this->obTARRPermissao  = new TARRPermissao ;
    // regras
    $this->obRARRGrupo      = new RARRGrupo     ;
    $this->obRCGM           = new RCGM          ;
    // transação
    $this->obTransacao      = new Transacao     ;
    // array de grupos
    $this->arGrupos         = array();
    $this->setPermitido( 'false' );

}

/**
    * Inclui os dados re
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function definirPermissao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    // verificar se ja existe ,
    if ( !$obErro->ocorreu() ) {
        // limpa permissoes do cgm;
        $tmpChave = $this->obTARRPermissao->getComplementoChave();

        $this->obTARRPermissao->setComplementoChave("numcgm");
        $this->obTARRPermissao->setDado  ( "numcgm"     , $this->obRCGM->getNumCGM() );

        $obErro = $this->obTARRPermissao->exclusao($boTransacao);

        $this->obTARRPermissao->setComplementoChave($tmpChave);

        if ( !$obErro->ocorreu() ) {
            foreach ($this->arGrupos as $arGrupos) {
                //inclusao de permissao
                $this->obRARRGrupo->setCodGrupo( $arGrupos["codgrupo"] );
                //$this->obRCGM->setNumCGM( $arGrupos["numcgm"] );
                $this->obTARRPermissao->setDado  ( "ano_exercicio", $arGrupos["exercicio"] );
                $this->obTARRPermissao->setDado  ( "cod_grupo"  , $this->obRARRGrupo->getCodGrupo () );
                $this->obTARRPermissao->setDado  ( "numcgm"     , $this->obRCGM->getNumCGM() );
                $obErro = $this->obTARRPermissao->inclusao($boTransacao);
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCreditoGrupo );

    return $obErro;
}
/**
    * Lista PERMISSOES
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPermissoes(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRARRGrupo->inCodGrupo) {
        $stFiltro .= "\n AND  agc.cod_grupo = ".$this->obRARRGrupo->inCodGrupo."";
    }
    if ($this->obRCGM->inNumCGM || $this->obRCGM->inNumCGM == '0') {
        $stFiltro .= "\n AND  cgm.numcgm = '".$this->obRCGM->inNumCGM."'::integer ";
    }
    $stOrdem = " ORDER BY agc.cod_grupo ";

   $obErro = $this->obTARRPermissao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
// $this->obTARRPermissao->debug();
   return $obErro;
}

/**
    * Recupera/consulta permissao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarPermissao($boTransacao = "")
{
    $obErro = new Erro;
    if ( $this->obRARRGrupo->getCodGrupo() && ($this->obRCGM->getNumCGM() ||  $this->obRCGM->getNumCGM() == 0) ) {
        $obErro = $this->listarPermissoes( $rsPermissao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRARRGrupo->inCodGrupo = $rsPermissao->getCampo( "cod_grupo"   );
            $this->obRCGM->inNumCGM        = $rsPermissao->getCampo( "numcgm"      );
            $this->boPermitido             = "true";
        } else {
            $this->boPermitido             = "false";
        }

    } else {
        $obErro->setDescricao("Código do Grupo de Créditos e Número do CGM devem estar setado!");
    }

    return $obErro;
}

} // fecha classe
?>
