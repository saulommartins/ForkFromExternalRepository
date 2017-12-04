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
* Gerar o componente composto por um combo de mês e todos os meses do ano
* Data de Criação: 01/07/2005

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Lucas Leusin Oaigen

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Gerar o componente composto por um combo e suas opções de mês
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package framework
    * @subpackage componentes
*/
class Mes extends Componente
{
/**
    * @access Private
    * @var Object
*/
var $obMes;
/**
    * @access Private
    * @var String
*/
var $stExercicio;
/**
    * @access Private
    * @var String
*/
var $boPeriodo;
/**
    * @access Private
    * @var Object
*/
var $obDataInicial;
/**
    * @access Private
    * @var Object
*/
var $obDataFinal;

/**
    * @access Public
    * @param Object $valor
*/
function setMes($valor) { $this->obMes   = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPeriodo($valor) { $this->boPeriodo = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDataInicial($valor) { $this->obDataInicial   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDataFinal($valor) { $this->obDataFinal   = $valor; }

/**
    * @access Public
    * @return Object
*/
function getMes() { return $this->obMes;   }
/**
    * @access Public
    * @return Object
*/
function getExercicio() { return $this->stExercicio;   }
/**
    * @access Public
    * @return Object
*/
function getPeriodo() { return $this->boPeriodo;   }
/**
    * @access Public
    * @return Object
*/
function getDataInicial() { return $this->obDataInicial;   }
/**
    * @access Public
    * @return Object
*/
function getDataFinal() { return $this->obDataFinal;   }

/**
    * Método Construtor
    * @access Public
*/
function Mes()
{
    parent::Componente();
    $this->setDefinicao("MES");

    if(!$this->getRotulo())
        $this->setRotulo("Mês");

    if(!$this->getTiTle())
        $this->setTitle( "Selecione o mês"  );

    $this->setName                      ( "inMes"           );
    $this->setMes                       ( new Select        );
    $this->obMes->setRotulo             ( "Mês"             );
    $this->obMes->setName               ( "inMes"           );
    $this->obMes->addOption             ( "","Selecione"    );
    $this->obMes->addOption             ( "01", "Janeiro"   );
    $this->obMes->addOption             ( "02", "Fevereiro" );
    $this->obMes->addOption             ( "03", "Março"     );
    $this->obMes->addOption             ( "04", "Abril"     );
    $this->obMes->addOption             ( "05", "Maio"      );
    $this->obMes->addOption             ( "06", "Junho"     );
    $this->obMes->addOption             ( "07", "Julho"     );
    $this->obMes->addOption             ( "08", "Agosto"    );
    $this->obMes->addOption             ( "09", "Setembro"  );
    $this->obMes->addOption             ( "10", "Outubro"   );
    $this->obMes->addOption             ( "11", "Novembro"  );
    $this->obMes->addOption             ( "12", "Dezembro"  );
    $this->obMes->setStyle              ( "width: 100px"    );

    $this->setDataInicial               ( new Hidden        );
    $this->obDataInicial->setName       ("stDataInicial"    );

    $this->setDataFinal                 ( new Hidden        );
    $this->obDataFinal->setName         ("stDataFinal"      );

}

/**
    * Monta o HTML do Objeto Mes
    * @access Protected
*/
function montaHtml()
{
    $stHtml = "";
    //MONTA O MES
    $this->obMes->setName( $this->getName() );

    if($this->getValue())
        $this->obMes->setValue ($this->getValue());

    /*
    O setPeriodo tem como valor default false, este parametro é usado para que
    sejam montados dois campos HIDDEN de periodo de acordo com o mês.
    IMPORTANTE: Para utilizar este método o exercício de ser setado.
    */
    if ($this->getPeriodo() and $this->getExercicio()) {
        if (!$this->obDataInicial->getName()) {
            $this->obDataInicial->setName       ("stDataInicial"    );
        }

        if (!$this->obDataFinal->getName()) {
            $this->obDataFinal->setName         ("stDataFinal"      );
        }

        $this->obMes->obEvento->setOnChange("preencheMes(this.value)");

        $this->obDataInicial->montaHTML();
        $stHtml = $this->obDataInicial->getHTML();

        $this->obDataFinal->montaHTML();
        $stHtml .= $this->obDataFinal->getHTML();

        echo "
          <script>
              function preencheMes(opcao)
              {
                      if (document.forms[0].".$this->obMes->getName().".value != '') {
                          document.forms[0].".$this->obDataInicial->getName().".value = '01/'+document.forms[0].".$this->obMes->getName().".value+'/".$this->getExercicio()."';
                          document.forms[0].".$this->obDataFinal->getName().".value = verificaDia(document.forms[0].".$this->obMes->getName().".value,'".$this->getExercicio()."')+'/'+document.forms[0].".$this->obMes->getName().".value+'/".$this->getExercicio()."';
                      } else {
                          document.forms[0].".$this->obDataInicial->getName().".value = '';
                          document.forms[0].".$this->obDataFinal->getName().".value = '';
                      }
              }

              function verificaDia(mes,ano)
              {
                  if (mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12) {
                      dia = 31;
                  } else if (mes == 4 || mes == 6 || mes == 9 || mes == 11) {
                      dia = 30;
                  } else {
                      if (ano % 4 != 0) {
                          dia = 28;
                      } else {
                          if (ano % 100 != 0)
                              dia = 29;
                          else
                              if (ano % 400 != 0 )
                                  dia = 28;
                              else
                                  dia = 29;
                      }
                  }

                  return dia;
              }
            </SCRIPT>
         ";

    }

    $this->obMes->montaHTML();
    $stHtml .= $this->obMes->getHTML();

    $this->setHtml($stHtml);

}
}

?>
