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
    * Classe de Exportação Arquivos de Relacionamento
    * Data de Criação   : 02/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Exportador

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.03
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.10  2006/07/17 14:31:02  cako
Bug #6013#

Revision 1.9  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

/* Includes */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

/**
   * Classe de Regra para geração de arquivo de Relacionamento para o TCE-RS
   * @author   Desenvolvedor :   Cleisson da Silva Barboza
   * @author   Analista      :   Diego Barbosa Victoria
*/
class RExportacaoTcersArquivosRelacionamento
{
   // Valores de entrada
   public $stExercicio;
   public $arArquivos;
   public $stDataInicial  ;
   public $stDataFinal    ;
   public $inOrgaoUnidade ;
   public $stCodEntidade ;
   public $obTContabilidadePlanoConta;

   // SETANDO
   public function setExercicio($valor) {   $this->stExercicio      = $valor;   }
   public function setArquivos($valor) {   $this->arArquivos       = $valor;   }
   public function setDataInicial($valor) {   $this->stDataInicial    = $valor;   }
   public function setDataFinal($valor) {   $this->stDataFinal      = $valor;   }
   public function setOrgaoUnidade($valor) {   $this->inOrgaoUnidade   = $valor;   }
   public function setCodEntidade($valor) {   $this->stCodEntidade    = $valor;   }

   // GETANDO
   public function getExercicio() {   return $this->stExercicio;      }
   public function getArquivos() {   return $this->arArquivos;       }
   public function getDataInicial() {   return $this->stDataInicial;    }
   public function getDataFinal() {   return $this->stDataFinal;      }
   public function getOrgaoUnidade() {   return $this->inOrgaoUnidade;   }
   public function getCodEntidade() {   return $this->stCodEntidade;    }

   /**
    * Metodo Construtor
    * @access Private
    */
    public function RExportacaoTcersArquivosRelacionamento()
    {
        $this->obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    // Gerando Recordset
    public function geraRecordset(&$arRecordset)
    {
        if (in_array("CTA_DISP.TXT",$this->getArquivos())) {
            $this->obTContabilidadePlanoConta->setDado('stExercicio', $this->getExercicio()     );
            $this->obTContabilidadePlanoConta->setDado('dtInicial', $this->getDataInicial()     );
            $this->obTContabilidadePlanoConta->setDado('dtFinal', $this->getDataFinal()         );
            $obErro = $this->obTContabilidadePlanoConta->recuperaDadosExportacao($rsRecordSet   );
            $arRecordset["CTA_DISP.TXT"] = $rsRecordSet;
        }
        if (in_array("CTA_OPER.TXT",$this->getArquivos())) {
            $arRecordset["CTA_OPER.TXT"] = new RecordSet();
        }

        return $obErro;
    }

    public function geraRecordsetAjustes(&$arRecordset)
    {
        if (in_array("CTA_DISP.TXT",$this->getArquivos())) {
            $this->obTContabilidadePlanoConta->setDado('stExercicio', $this->getExercicio()     );
            $this->obTContabilidadePlanoConta->setDado('dtInicial', $this->getDataInicial()     );
            $this->obTContabilidadePlanoConta->setDado('dtFinal', $this->getDataFinal()         );
//          $this->obTContabilidadePlanoConta->setDado('inOrgaoUnidade', $this->getOrgaoUnidade() );
            $this->obTContabilidadePlanoConta->setDado('stCodEntidade', $this->stCodEntidade    );
            if ($this->getExercicio()=="2005") {
                $obErro = $this->obTContabilidadePlanoConta->recuperaDadosExportacaoAjustes2005($rsRecordSet   );
            } else {
                $obErro = $this->obTContabilidadePlanoConta->recuperaDadosExportacaoAjustes($rsRecordSet   );
            }
            $arRecordset["CTA_DISP.TXT"] = $rsRecordSet;
        }
        if (in_array("CTA_OPER.TXT",$this->getArquivos())) {
            $arRecordset["CTA_OPER.TXT"] = new RecordSet();
        }

        return $obErro;
    }
}
