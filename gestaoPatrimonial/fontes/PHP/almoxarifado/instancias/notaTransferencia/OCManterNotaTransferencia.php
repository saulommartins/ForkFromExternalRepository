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
    * Página de Oculto para Nota de Transferência
    * Data de criação : 17/04/2006

    * @author Analista   : Diego Victoria
    * @author Programador: Rodrigo

    * @ignore

    Caso de uso: uc-03.03.08

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoItemMarca.class.php"                               );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php"                             );
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPedidoTransferencia.class.php"                     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaTransferencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once ($pgJS);

$stCtrl                 = $_REQUEST['stCtrl'];
$rsMarcas               = new RecordSet;
$obRPedidoTransferencia = new RAlmoxarifadoPedidoTransferencia;

function montaListaItens($arRecordSet , $boExecuta = true)
{
    $rsItens = new RecordSet;
    $rsItens->preenche( $arRecordSet );

    $rsItens->setPrimeiroElemento();

    $obLista = new Lista;
    $obLista->setTitulo('');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsItens );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth   (5       );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Item");
    $obLista->ultimoCabecalho->setWidth   (25    );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Marca");
    $obLista->ultimoCabecalho->setWidth   (15     );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Centro de Custo");
    $obLista->ultimoCabecalho->setWidth   (25               );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Saldo");
    $obLista->ultimoCabecalho->setWidth   (5      );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth   (5           );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    if ($boExecuta) {
       $obLista->ultimoCabecalho->addConteudo("&nbsp;");
       $obLista->ultimoCabecalho->setWidth   (5       );
       $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ("[cod_item]-[descricao_item]");
    $obLista->ultimoDado->setAlinhamento('CENTRO'                     );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ("descricao_marca");
    $obLista->ultimoDado->setAlinhamento('CENTRO'         );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ("[cod_centro]-[descricao_centro]");
    $obLista->ultimoDado->setAlinhamento('CENTRO'                         );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ("saldo"  );
    $obLista->ultimoDado->setAlinhamento('DIREITA');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo      ("quantidade");
    $obLista->ultimoDado->setAlinhamento('DIREITA'   );
    $obLista->commitDado();

    if ($boExecuta) {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao  ("EXCLUIR"                  );
        $obLista->ultimaAcao->setFuncao(true                       );
        $obLista->ultimaAcao->setLink  ("JavaScript:excluirItem( );");
        $obLista->ultimaAcao->addCampo ("1","id"                   );
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n" ,"" ,$stHTML);
    $stHTML = str_replace("  " ,"" ,$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto("d.getElementById('spnItens').innerHTML = '".$stHTML."';");
    } else {
        return $stHTML;
    }
 }

function montaAlmoxarifados($boLabel = false, $origem = '',$destino = '')
{
    $obRAlmoxarifado = new RAlmoxarifadoAlmoxarifado;
    $obRAlmoxarifado->listar($rsAlmoxarifados);

    $obRAlmoxarifadoAlmoxarife = new RAlmoxarifadoAlmoxarife;
    $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCgm(Sessao::read('numCgm'));
    $obRAlmoxarifadoAlmoxarife->listarPermissao($rsAlmoxarifadoAlmoxarife,"",true);
    $obRAlmoxarifadoAlmoxarife->consultar();
    $inCodAlmoxarifadoPadrao = $obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->getCodigo();

    $obForm       = new Form;
    $obFormulario = new Formulario;

    if (!$boLabel) {
        $obCmbAlmoxarifadoOrigem = new Select;
        $obCmbAlmoxarifadoOrigem->setRotulo            ('Almoxarifado de Origem'                                                          );
        $obCmbAlmoxarifadoOrigem->setTitle             ("Selecione o almoxarifado de origem.");
        $obCmbAlmoxarifadoOrigem->setName              ('inCodAlmoxarifadoOrigem'            );
        if($origem)
           $obCmbAlmoxarifadoOrigem->setValue             ($origem                                                                      );
        else
           $obCmbAlmoxarifadoOrigem->setValue             ($inCodAlmoxarifadoPadrao                                                     );

        $obCmbAlmoxarifadoOrigem->setNull              (false                                                                             );
        $obCmbAlmoxarifadoOrigem->setCampoID           ('codigo'                                                                          );
        $obCmbAlmoxarifadoOrigem->setCampoDesc         ('[codigo]-[nom_a]'                                                                );
        $obCmbAlmoxarifadoOrigem->addOption            ("", "Selecione"                                                                   );
        $obCmbAlmoxarifadoOrigem->preencheCombo        ($rsAlmoxarifados                                                                  );
        $obCmbAlmoxarifadoOrigem->obEvento->setOnChange("limpaCamposItens()");

        $obCmbAlmoxarifadoDestino = new Select;
        $obCmbAlmoxarifadoDestino->setRotulo            ('Almoxarifado de Destino'                                                         );
        $obCmbAlmoxarifadoDestino->setTitle             ("Selecione o almoxarifado de destino.");
        $obCmbAlmoxarifadoDestino->setName              ('inCodAlmoxarifadoDestino'            );
        if($destino)
           $obCmbAlmoxarifadoDestino->setValue             ($destino                                                                       );
        else
           $obCmbAlmoxarifadoDestino->setValue             ($inCodAlmoxarifadoPadrao                                                     );
        $obCmbAlmoxarifadoDestino->setNull              (false                                                                             );
        $obCmbAlmoxarifadoDestino->setCampoID           ('codigo'                                                                          );
        $obCmbAlmoxarifadoDestino->setCampoDesc         ('[codigo]-[nom_a]'                                                                );
        $obCmbAlmoxarifadoDestino->addOption            ("", "Selecione"                                                                   );
        $obCmbAlmoxarifadoDestino->preencheCombo        ($rsAlmoxarifadoAlmoxarife                                                         );
    } else {
        include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAlmoxarifado.class.php"                             );
        $obTAlmoxarifadoAlmoxarifado = new TAlmoxarifadoAlmoxarifado;
        $stFiltro = ' and a.cod_almoxarifado = '.$origem;
        $obTAlmoxarifadoAlmoxarifado->recuperaRelacionamento($rsAlmoxarifadoOrigem, $stFiltro);
        $stAlmoxarifadoOrigem = $origem.'-'.$rsAlmoxarifadoOrigem->getCampo('nom_a');

        $obCmbAlmoxarifadoOrigem = new Label;
        $obCmbAlmoxarifadoOrigem->setRotulo('Almoxarifado de Origem');
        $obCmbAlmoxarifadoOrigem->setId    ('stAlmoxarifadoOrigem'  );
        $obCmbAlmoxarifadoOrigem->setValue ($stAlmoxarifadoOrigem   );

        $obTAlmoxarifadoAlmoxarifado = new TAlmoxarifadoAlmoxarifado;
        $stFiltro = ' and a.cod_almoxarifado = '.$destino;
        $obTAlmoxarifadoAlmoxarifado->recuperaRelacionamento($rsAlmoxarifadoDestino, $stFiltro);
        $stAlmoxarifadoDestino = $destino.'-'.$rsAlmoxarifadoDestino->getCampo('nom_a');

        $obCmbAlmoxarifadoDestino = new Label;
        $obCmbAlmoxarifadoDestino->setRotulo('Almoxarifado de Destino');
        $obCmbAlmoxarifadoDestino->setId    ('stAlmoxarifadoDestino'  );
        $obCmbAlmoxarifadoDestino->setValue ($stAlmoxarifadoDestino   );

        $obHdnCodAlmoxarifadoOrigem = new Hidden;
        $obHdnCodAlmoxarifadoOrigem->setName ("inCodAlmoxarifadoOrigem");
        $obHdnCodAlmoxarifadoOrigem->setValue( $origem );

        $obHdnCodAlmoxarifadoDestino = new Hidden;
        $obHdnCodAlmoxarifadoDestino->setName ("inCodAlmoxarifadoDestino");
        $obHdnCodAlmoxarifadoDestino->setValue( $destino);
    }
        $obFormulario->addForm      ($obForm                  );
        $obFormulario->addComponente($obCmbAlmoxarifadoOrigem );
        $obFormulario->addComponente($obCmbAlmoxarifadoDestino);
        if ($boLabel) {
           $obFormulario->addHidden    ($obHdnCodAlmoxarifadoOrigem );
           $obFormulario->addHidden    ($obHdnCodAlmoxarifadoDestino );
        }

    $obFormulario->montaInnerHTML();
    $HTML = $obFormulario->getHTML();

    $obFormulario->obJavaScript->montaJavaScript();
    $stValida = $obFormulario->obJavaScript->getInnerJavaScript();

    $stJs = "d.getElementById('spnAlmoxarifado').innerHTML = '".$HTML."';                      \n";
    $stJs.= "f.stValida.value = '".$stValida."';";
    $stJs.= "parent.window.frames['telaPrincipal'].document.frm.inCodAlmoxarifadoOrigem.focus(); \n";

    return $stJs;
 }

 function Resetar($inVar)
 {
    switch ($inVar) {

        case 1:

            $stJs .= "f.inCodItem.value = '';                                   \n";
            $stJs .= "d.getElementById('stNomItem').innerHTML       = '&nbsp;'; \n";
            $stJs .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;'; \n";
            $stJs .= "limpaSelect(f.inCodMarca, 1 );                            \n";
            $stJs .= "limpaSelect(f.inCodCentroCusto, 1 );                      \n";
            $stJs .= "limpaSelect(f.inCodCentroCustoDestino, 1 );               \n";
            $stJs .= "d.getElementById('inSaldo').innerHTML         = '&nbsp;'; \n";
            $stJs .= "f.nuQuantidade.value = '';                                \n";
        break;

        case 2:
            $stJs .= "d.getElementById('inSaldo').innerHTML = '&nbsp;';         \n";
            $stJs .= "f.nuQuantidade.value                  = '';               \n";
        break;
    }

    return $stJs;
 }

 switch ($stCtrl) {

    case 'carregaItem':
        foreach (Sessao::read('arItens') as $key => $value ) {
            if ( ($key+1) != $_REQUEST['id'] ) {
                $inCodItem = $value['cod_item'        ];
                 $value['cod_centro'      ];
                 $value['cod_marca'       ];
                $stDescricaoItem = $value['descricao_item'  ];
                 $value['descricao_marca' ];
                 $value['descricao_centro'];
                $nmSaldo      = number_format($value['saldo'     ], 4, ',', '.');
                $nmQuantidade = number_format($value['quantidade'], 4, ',', '.');
            }
        }

        $stJs .= " f.inCodItem.value = ".$inCodItem.";";
        $stJs .= " d.getElementById('stUnidadeMedida').innerHTML = '".$stUnidadeMedida."';";
        $stJs .= " d.getElementById('stNomItem').innerHTML = '".$stDescricaoItem."';";
        $stJs .= " d.getElementById('inSaldo').innerHTML = '".$nmSaldo."'; ";
        $stJs .= " f.nuQuantidade.value = '".$nmQuantidade."';\n ";
    break;

    case 'preencheItens':
      $obRPedidoTransferencia->addPedidoTransferenciaItem();
      $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->listar($rsItens);
    break;

    case 'carregaMarca':
      $stJs .= 'limpaSelect(f.inCodMarca,1);';
      $stJs .= 'limpaSelect(f.inCodCentroCusto,1);';
      if ($_REQUEST['inCodItem']) {
         $obRPedidoTransferencia->addPedidoTransferenciaItem();
         $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($_REQUEST['inCodItem']);
         $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->listar($rsMarcas);
         $rsMarcas->setPrimeiroElemento();

         while ( !$rsMarcas->eof() ) {
             $stJs .= "f.inCodMarca[".$rsMarcas->getCorrente()."] = new Option('".$rsMarcas->getCampo('descricao')."','".$rsMarcas->getCampo('cod_marca')."');\n";
             $rsMarcas->proximo();
         }
      }

    break;

    case 'carregaCentroCusto':
      $stJs .= 'limpaSelect(f.inCodCentroCusto, 1);';

      if (!empty($_REQUEST['inCodMarca']) && $_REQUEST['inCodAlmoxarifadoOrigem']) {
          $obRPedidoTransferencia->addPedidoTransferenciaItem();

          $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($_REQUEST['inCodMarca']                    );
          $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->setCodigo($_REQUEST['inCodAlmoxarifadoOrigem']);
          $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($_REQUEST['inCodItem']             );

          $obErro = $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->listarCentroDeCustoAlmoxarifado($rsCentro);

          if (!($obErro->ocorreu())) {
           $rsCentro->setPrimeiroElemento();
           while (!$rsCentro->eof()) {

            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($_REQUEST['inCodCentroCusto']);
            $obErro = $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->retornaSaldoEstoque($inSaldo);

            ($inSaldo=="" | $inSaldo==null | $inSaldo=="0.0000")?$inSaldo=0:$inSaldo=$inSaldo;
            if (!($inSaldo==0)) {
             $stJs.="f.inCodCentroCusto[".$rsCentro->getCorrente()."]=new Option('".$rsCentro->getCampo('descricao')."','".$rsCentro->getCampo('cod_centro')."');\n";
            }
            $rsCentro->proximo();
           }
             $stJs .= "f.stMarca.value = f.inCodMarca.options[f.inCodMarca.selectedIndex].text;";
         }
      }
      $stJs .= Resetar(2);
    break;

    case 'mostraSaldo':
      if (!empty($_REQUEST['inCodCentroCusto'])) {

         $obRPedidoTransferencia->addPedidoTransferenciaItem();
         $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($_REQUEST['inCodCentroCusto']     );
         $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo($_REQUEST['inCodMarca']                    );
         $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($_REQUEST['inCodItem']              );
         $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRAlmoxarifado->setCodigo($_REQUEST['inCodAlmoxarifadoOrigem']);

         $obErro = $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->retornaSaldoEstoque($inSaldo);
         $inSaldo = ($inSaldo == "" | $inSaldo == null)? number_format(0, 4, ',', '.') : number_format($inSaldo, 4, ',', '.');
         $stJs  .= "d.getElementById('inSaldo').innerHTML = '".$inSaldo."';         ";
         $stJs  .= "f.stSaldo.value = '".$inSaldo."';                                                           ";
         $stJs  .= "f.stCentroCusto.value = f.inCodCentroCusto.options[f.inCodCentroCusto.selectedIndex].text;";

         include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoCentroCusto.class.php';
         $obTAlmoxaridadoCentroCusto = new TAlmoxarifadoCentroCusto;
         if ($_REQUEST['inCodAlmoxarifadoOrigem'] == $_REQUEST['inCodAlmoxarifadoDestino']) {
            $stFiltro = ' WHERE centro_custo.cod_centro <> '.$_REQUEST['inCodCentroCusto'];
         }

         $stOrder = ' centro_custo.cod_centro ';
         $rsCentros = new RecordSet;
         $obTAlmoxaridadoCentroCusto->recuperaTodos($rsCentros, $stFiltro, 'descricao');

         $stJs .= 'limpaSelect(f.inCodCentroCustoDestino, 0);';
         $stJs .= 'f.inCodCentroCustoDestino.options[0] = new Option("Selecione", "");';
         $inCountCentro = 1;
         while (!($rsCentros->eof())) {
            $inCodCentro = $rsCentros->getCampo('cod_centro');
            $stDescricaoCentro = $rsCentros->getCampo('descricao');
            $stJs .= 'f.inCodCentroCustoDestino.options['.$inCountCentro.'] = new Option("'.$stDescricaoCentro.'", "'.$inCodCentro.'");';
            $inCountCentro++;
            $rsCentros->Proximo();
         }
      } else {
         $stJs .= Resetar(2);
      }
    break;

    case 'buscaCentroCusto':
        if (!empty($_REQUEST['inCodCentroCusto'])) {
            $obRPedidoTransferencia->addPedidoTransferenciaItem();
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo($_REQUEST['inCodCentroCusto']);
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->consultar();
            $stJs .= "d.getElementById('stNomCentroCusto').innerHTML = '".$obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getDescricao()."';";
        } else {
            $stJs .= "d.getElementById('stNomCentroCusto').innerHTML = '&nbsp;';";
        }
    break;

    case 'buscaMarca':
        if (!empty($_REQUEST['inCodMarca'])) {
          $obRPedidoTransferencia->addPedidoTransferenciaItem();
          $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obREstoqueItem->obRMarca->setCodigo( $_REQUEST['inCodMarca'] );
          $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obREstoqueItem->obRMarca->consultar();
          $stJs .= "d.getElementById('stNomMarca').innerHTML = '".$obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obREstoqueItem->obRMarca->getDescricao()."';";
        } else {
          $stJs .= "d.getElementById('stNomMarca').innerHTML = '&nbsp;';";
        }
    break;

    case 'buscaItem':
        if (!empty($_REQUEST['inCodItem'])) {
            $obRPedidoTransferencia->addPedidoTransferenciaItem();
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($_REQUEST['inCodItem']);
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->consultar();
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->listar($rsItem);
            $stJs .= 'd.getElementById("stNomItem").innerHTML = "'.$obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getDescricao().'";';
            $stJs .= "d.getElementById('stUnidadeMedida').innerHTML = '".$rsItem->getCampo('nom_unidade')."';                                                   \n";
            $stJs .= "f.stItem.value = '".$obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getDescricao()."';         \n";
            $stJs .= "limpaSelect(f.inCodCentroCusto, 1 );                                                                                                      \n";
            $stJs .= "limpaSelect(f.inCodMarca, 1 );                                                                                                            \n";
            $stJs .= "setTimeout('buscaItemMarca()',1000);                                                                                                      \n";
        } else {
            $stJs .= "d.getElementById('stNomItem').innerHTML       = '&nbsp;';                                                                                   ";
            $stJs .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';                                                                                   ";
            $stJs .= Resetar(1);
        }
            $stJs .= "";
    break;

    case 'alterarItem':

    break;

    case 'incluirItem':
        $flQuantidade = str_replace('.', '', $_REQUEST['nuQuantidade']);
        $flQuantidade = str_replace(',', '.', $flQuantidade);
        $flSaldo = str_replace('.', '', $_REQUEST['stSaldo']);
        $flSaldo = str_replace(',', '.', $flSaldo);

        $arItens = Sessao::read('arItens');

        if ($flQuantidade <= 0) {
            $stMensagem = "Quantidade não pode ser nula.";
        } else {
            if ((floatVal($flQuantidade) > floatVal($flSaldo)) && ( !$stMensagem )) {
                $stMensagem = 'Quantidade '.number_format($flQuantidade, 4, ',', '.').' deve ser menor ou igual ao Saldo em Estoque '.number_format($flSaldo, 4, ',', '.').'.';
            }

            if ((count(Sessao::read('arItens'))>0) && ( !$stMensagem )) {
                foreach (Sessao::read('arItens') as $arTEMP) {
                   if (($arTEMP['cod_item']==$_POST['inCodItem'])&&($arTEMP['cod_centro'] == $_POST['inCodCentroCusto'])&&($arTEMP['cod_marca']==$_POST['inCodMarca'])) {
                        $stMensagem = "Não pode haver mais de um ítem da mesma marca neste centro de custo." ;
                     break;
                   }
                }
            }
        }
        if ((empty($_POST['inCodItem'])||empty($_POST['inCodMarca']))||(empty($_POST['inCodCentroCusto'])||empty($_POST['nuQuantidade']))) {
            $stMensagem = "Existem valores nulos.";
        }
        if (empty($_POST['inCodCentroCusto'])) {
            $stMensagem = 'Quantidade '.number_format($flQuantidade, 4, ',', '.').' deve ser menor ou igual ao Saldo em Estoque '.number_format($flSaldo, 4, ',', '.').'.';
        }
        if (empty($_REQUEST['inCodCentroCustoDestino'])) {
            $stMensagem = 'Selecione o Centro de Custo Destino';
        }
        if (empty($_POST['inCodAlmoxarifadoOrigem'])) {
            $stMensagem = "Selecione o almoxarifado de origem.";
        }
        if (empty($_POST['inCodAlmoxarifadoDestino'])) {
            $stMensagem = "Selecione o almoxarifado de destino.";
        }
        if(($_POST['inCodAlmoxarifadoOrigem']==$_POST['inCodAlmoxarifadoDestino']
         && ($_REQUEST['inCodCentroCusto']==$_REQUEST['inCodCentroCustoDestino'])) && ( !$stMensagem )){
            $stMensagem = "Centro de Custo de origem deve ser diferente do Centro de Custo de destino.";
        }
        if (empty($stMensagem)) {
            $obRPedidoTransferencia->addPedidoTransferenciaItem();
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo($_REQUEST['inCodItem']);
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->consultar();
            $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->listar($rsItem);

            $stItem   = $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->getDescricao();
            $stMarca  = $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRMarca->getDescricao();
            $stCentro = $obRPedidoTransferencia->roUltimoPedidoTransferenciaItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->getDescricao();
            $inCount = sizeof($arItens);

            $arItens[$inCount]['id'                   ] = $inCount+1;
            $arItens[$inCount]['cod_item'             ] = $_POST['inCodItem'        ];
            $arItens[$inCount]['descricao_item'       ] = $_POST['stNomItem'        ];
            $arItens[$inCount]['descricao_marca'      ] = $_POST['stMarca'          ];
            $arItens[$inCount]['cod_centro'           ] = $_POST['inCodCentroCusto' ];
            $arItens[$inCount]['cod_centro_destino'   ] = $_POST['inCodCentroCustoDestino' ];
            $arItens[$inCount]['descricao_centro'     ] = $_POST['stCentroCusto'    ];
            if ($_REQUEST['stAcao'] == 'alterar') {
                $arItens[$inCount]['saldo'            ] = $flSaldo;
                $arItens[$inCount]['quantidade'       ] = $flQuantidade;
            } else {
                $arItens[$inCount]['saldo'            ] = number_format($flSaldo,4,',','.');
                $arItens[$inCount]['quantidade'       ] = number_format($flQuantidade,4,',','.');
            }
            $arItens[$inCount]['cod_marca'            ] = $_POST['inCodMarca'       ];
            $arItens[$inCount]['cod_almoxarifado'     ] = $_POST['inCodAlmoxarifado'];
            $arItens[$inCount]['valores_atributos'    ] = Sessao::read('obIMontaItemQuantidadeValoresAtributo');

            if ($_REQUEST['stAcao'] == 'alterar') {
                foreach ($arItens as $inChave => $arItem) {
                    foreach ($arItem as $stChave => $valor) {
                        if (($stChave == 'quantidade')||($stChave == 'saldo')) {
                            $arItens[$inChave][$stChave] = number_format($valor,4,',','.');
                        }
                    }
                }
            }

            Sessao::write('arItens',$arItens);
            montaListaItens(Sessao::read('arItens'));
            $stJs .= Resetar(1);
            sistemaLegado::executaFrameOculto(montaAlmoxarifados(true,$_POST['inCodAlmoxarifadoOrigem'],$_POST['inCodAlmoxarifadoDestino']));
        } else {
            $stJs .= "alertaAviso('@Valor inválido. (".$stMensagem.")','form','erro','".Sessao::getId()."');";
        }
        $stMensagem = "";
    break;

    case 'excluirItem':
        $arTEMP  = array();
        $inCount = 0;
        foreach ( Sessao::read('arItens') as $key => $value ) {
            if ( ($key+1) != $_REQUEST['id'] ) {
                $arTEMP[$inCount]['id'              ] = $inCount+1;
                $arTEMP[$inCount]['cod_item'        ] = $value['cod_item'        ];
                $arTEMP[$inCount]['cod_centro'      ] = $value['cod_centro'      ];
                $arTEMP[$inCount]['cod_centro_destino'] = $value['cod_centro_destino'];
                $arTEMP[$inCount]['cod_marca'       ] = $value['cod_marca'       ];
                $arTEMP[$inCount]['descricao_item'  ] = $value['descricao_item'  ];
                $arTEMP[$inCount]['descricao_marca' ] = $value['descricao_marca' ];
                $arTEMP[$inCount]['descricao_centro'] = $value['descricao_centro'];
                $arTEMP[$inCount]['saldo'           ] = $value['saldo'           ];
                $arTEMP[$inCount]['quantidade'      ] = $value['quantidade'      ];
                $inCount++;
            }
        }
        Sessao::write('arItens',$arTEMP);
        $boLabel = count(Sessao::read('arItens')) > 0;

        $arItens = Sessao::read('arItens');

        foreach ($arItens as $chave => $arItem) {
            $arItens[$chave]['quantidade'] = number_format($arItem['quantidade'],4,',','.');
            $arItens[$chave]['saldo']      = number_format($arItem['saldo'],4,',','.');
        }
        reset($arItens);
        montaListaItens($arItens);

        sistemaLegado::executaFrameOculto(montaAlmoxarifados($boLabel, $_POST['inCodAlmoxarifadoOrigem'], $_POST['inCodAlmoxarifadoDestino']));
    break;

    case 'montaAlmoxarifadosLabel':

      $arItens = Sessao::read('arItens');

      foreach ($arItens as $chave => $arItem) {
          $arItens[$chave]['quantidade'] = number_format($arItem['quantidade'],4,',','.');
          $arItens[$chave]['saldo'] = number_format($arItem['saldo'],4,',','.');
      }

      reset($arItens);
      $js = montaAlmoxarifados(true, $_GET['inCodAlmoxarifadoOrigem'], $_GET['inCodAlmoxarifadoDestino']);
      $js .= montaListaItens($arItens);

      sistemaLegado::executaFrameOculto($js);
    break;

    case 'montaAlmoxarifados':
      sistemaLegado::executaFrameOculto(montaAlmoxarifados());
    break;

    case 'limpaFormulario':
      Sessao::write('arItens',array());
      $stLimpa = "d.getElementById('stExercicio').innerHTML ='".Sessao::getExercicio()."';";
      sistemaLegado::executaFrameOculto(montaAlmoxarifados().$stLimpa);
    break;

 }

if (!empty($stJs)) {
 sistemaLegado::executaFrameOculto($stJs);
}
