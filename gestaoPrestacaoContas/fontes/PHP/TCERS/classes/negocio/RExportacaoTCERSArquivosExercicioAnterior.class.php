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
    * Classe de Exportação Arquivos Exercício Anterior
    * Data de Criação   : 04/02/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Exportador

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.07
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

/* Includes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                              );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeSamlinkSiamPlano.class.php"                         );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"                               );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php"                                       );
include_once( CAM_GPC_TCERS_MAPEAMENTO."FExportacaoTCERSExportacaoBalanceteReceita.class.php"              );
include_once( CAM_GPC_TCERS_MAPEAMENTO."FExportacaoTCERSExportacaoReceita.class.php"                       );
include_once( CAM_GPC_TCERS_MAPEAMENTO."FExportacaoBmovant.class.php"                                      );

class RExportacaoTCERSArquivosExercicioAnterior
{
    /* Valores entre*/
    public $stExercicio    ;
    public $arArquivos = array()     ;
    public $stHost;
    public $stPorta;
    public $stBanco;
    public $stUsuario;
    public $stCodEntidades;
    public $obTSamlinkSiamPlano  ;
    public $obTConfiguracao;
    public $obTContabilidadePlanoConta;
    public $stDataInicial;
    public $stDataFinal;
    public $inPeriodo;
    public $inTipoPeriodo;

    /**
    * Metodo Construtor
    * @access Private
    */
    public function RExportacaoTcersArquivosExercicioAnterior()
    {
        $this->obTSamlinkSiamPlano                          = new TContabilidadeSamlinkSiamPlano();
        $this->obTConfiguracao                              = new TAdministracaoConfiguracao();
        $this->obTContabilidadePlanoConta                   = new TContabilidadePlanoConta();
        $this->obFExportacaoTCERSExportacaoBalanceteReceita = new FExportacaoTCERSExportacaoBalanceteReceita();
        $this->obFExportacaoTCERSExportacaoReceita          = new FExportacaoTCERSExportacaoReceita();
        $this->obTOrcamentoDespesa                          = new TOrcamentoDespesa();
        $this->obFExportacaoBmovant                         = new FExportacaoBmovant();
    }

    // SETANDO
    public function setExercicio($valor) {   $this->stExercicio      =   $valor; }
    public function setArquivos($valor) {   $this->arArquivos       =   $valor; }
    public function setHost($valor) {   $this->stHost           =   $valor; }
    public function setPorta($valor) {   $this->stPorta          =   $valor; }
    public function setBanco($valor) {   $this->stBanco          =   $valor; }
    public function setUsuario($valor) {   $this->stUsuario        =   $valor; }
    public function setCodEntidades($valor) {   $this->stCodEntidades   =   $valor; }
    public function setDataInicial($valor) {   $this->stDataInicial    =   $valor; }
    public function setDataFinal($valor) {   $this->stDataFinal      =   $valor; }
    public function setPeriodo($valor) {   $this->inPeriodo        =   $valor; }
    public function setTipoPeriodo($valor) {   $this->inTipoPeriodo    =   $valor; }

    // GETANDO
    public function getExercicio() {   return $this->stExercicio;      }
    public function getArquivos() {   return $this->arArquivos;       }
    public function getHost() {   return $this->stHost;           }
    public function getPorta() {   return $this->stPorta;          }
    public function getBanco() {   return $this->stBanco;          }
    public function getUsuario() {   return $this->stUsuario;        }
    public function getCodEntidades() {   return $this->stCodEntidades;   }
    public function getDataInicial() {   return $this->stDataInicial;    }
    public function getDataFinal() {   return $this->stDataFinal;      }
    public function getPeriodo() {   return $this->inPeriodo;        }
    public function getTipoPeriodo() {   return $this->inTipoPeriodo;    }

    // Gerando Recordset
    public function geraRecordset(&$arRecordset)
    {
        if (in_array("BVMOVANT.TXT",$this->getArquivos())) {
            $this->obFExportacaoBmovant->setDado('stExercicio'     , $this->getExercicio()                );
            $this->obFExportacaoBmovant->setDado('stCodEntidades'  , $this->getCodEntidades()             );
            $obErro = $this->obFExportacaoBmovant->recuperaDadosExportacao($rsRecordset  );
            $arRecordset["BVMOVANT.TXT"] = $rsRecordset;
        }
        if (in_array("BRUB_ANT.TXT",$this->getArquivos())) {
            $this->obTOrcamentoDespesa->setDado('stExercicio'     , $this->getExercicio()       );
            $this->obTOrcamentoDespesa->setDado('stCodEntidades'  , $this->getCodEntidades()    );
            $obErro = $this->obTOrcamentoDespesa->recuperaExportacaoBrubAnt($rsRecordset         );
            $arRecordset["BRUB_ANT.TXT"] = $rsRecordset;
        }

        if (in_array("BREC_ANT.TXT",$this->getArquivos())) {
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('stCodEntidades', $this->getCodEntidades()      );
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro     =   $this->obFExportacaoTCERSExportacaoBalanceteReceita->recuperaDadosExportacao($rsRecordset    );
            $arRecordset["BREC_ANT.TXT"] = $rsRecordset;

        }
        if (in_array("REC_ANT.TXT",$this->getArquivos())) {
            $this->obFExportacaoTCERSExportacaoReceita->setDado('stExercicio'     , $this->getExercicio()               );
            $this->obFExportacaoTCERSExportacaoReceita->setDado('stCodEntidades'  , $this->getCodEntidades()            );
            $this->obFExportacaoTCERSExportacaoReceita->setDado('inBimestre'      , "6"                                 );
            $this->obFExportacaoTCERSExportacaoReceita->setDado('inPeriodo'       , $this->getPeriodo()                  );
            $this->obFExportacaoTCERSExportacaoReceita->setDado('inTipoPeriodo'   , $this->getTipoPeriodo()              );
            $obErro     =   $this->obFExportacaoTCERSExportacaoReceita->recuperaDadosExportacao($rsRecordset            );
            $arRecordset["REC_ANT.TXT"] = $rsRecordset;
        }
        if (in_array("BVER_ANT.TXT",$this->arArquivos)) {
            $this->obTContabilidadePlanoConta->setDado('stExercicio'        , $this->getExercicio()             );
            $this->obTContabilidadePlanoConta->setDado('dtInicial'          , $this->getDataInicial()           );
            $this->obTContabilidadePlanoConta->setDado('dtFinal'            , $this->getDataFinal()             );
            $this->obTContabilidadePlanoConta->setDado('stCodEntidades'     , $this->getCodEntidades()          );
            $obErro =   $this->obTContabilidadePlanoConta->recuperaDadosExportacaoBalVerificacao($rsRecordset   );
            $arRecordset["BVER_ANT.TXT"] = $rsRecordset;
        }

        return $obErro;
    }
}
